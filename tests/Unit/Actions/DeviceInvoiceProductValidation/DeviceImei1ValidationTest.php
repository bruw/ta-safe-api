<?php

namespace Tests\Unit\Actions\DeviceInvoiceProductValidation;

use App\Actions\DeviceInvoiceProductValidation\DeviceImei1ValidationAction;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Traits\StringNormalizer;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceImei1ValidationTest extends TestCase
{
    use RefreshDatabase;
    use StringNormalizer;

    private Brand $brand;
    private Device $device;
    private DeviceModel $deviceModel;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->brandSetUp();
        $this->deviceSetUp();
    }

    /*
    ================= **START OF SETUP** ==========================================================================
    */

    private function brandSetUp(): void
    {
        $this->brand = BrandFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->deviceModel = DeviceModelFactory::new()
            ->for($this->brand)
            ->create();

        $this->device = DeviceFactory::new()
            ->for(UserFactory::new()->create())
            ->for($this->deviceModel)
            ->create([
                'imei_1' => '123456789012345',
                'imei_2' => '123456789012350',
            ]);

        $this->invoice = InvoiceFactory::new()
            ->for($this->device)
            ->create([
                'product_description' => "{$this->brand->name}"
                . " {$this->device->deviceModel->name}"
                . " {$this->device->deviceModel->ram}"
                . " {$this->device->deviceModel->storage}"
                . " {$this->device->color}"
                . " {$this->device->imei_1}"
                . " {$this->device->imei_2}",
            ]);
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_must_validate_identical_imei_1(): void
    {
        $deviceImei1SimilarityValidator = new DeviceImei1ValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceImei1SimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_record_in_the_database_for_successful_validations(): void
    {
        $deviceImei1SimilarityValidator = new DeviceImei1ValidationAction(
            $this->device, $this->invoice->product_description
        );

        $deviceImei1SimilarityValidator->execute();

        $imei1 = $this->extractOnlyDigits($this->device->imei_1);
        $products = $this->extractOnlyDigits($this->invoice->product_description);

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Device::class,
            'attribute_label' => 'imei_1',
            'attribute_value' => $imei1,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $products,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 90,
            'validated' => true,
        ]);
    }

    public function test_must_be_able_to_validate_an_imei1_even_if_it_is_between_a_text(): void
    {
        $this->invoice->update([
            'product_description' => "Smartphone Galaxy S20 Ultra 5G, 128GB, Cosmic Gray, 12GB RAM, imei1 {$this->device->imei_1}",
        ]);

        $this->device->refresh();

        $deviceImei1SimilarityValidator = new DeviceImei1ValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceImei1SimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 90);
    }

    public function test_the_action_must_validate_an_imei1_with_a_similarity_ratio_greater_than_or_equal_90_percent(): void
    {
        $deviceImei1SimilarityValidator = new DeviceImei1ValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceImei1SimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 90);
    }

    public function test_the_action_must_not_validate_an_imei1_with_a_similarity_ratio_of_less_than_90_porcent(): void
    {
        $this->device->update([
            'imei_1' => '000000000000001',
        ]);

        $this->invoice->update([
            'product_description' => 'Smartphone Galaxy S20 Ultra IMEI 000000000000002',
        ]);

        $this->device->refresh();

        $deviceImei1SimilarityValidator = new DeviceImei1ValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceImei1SimilarityValidator->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 84);
        $this->assertEquals($result->min_similarity_ratio, 90);
    }

    public function test_should_not_validate_an_imei1_that_are_empty_strings(): void
    {
        $this->device->update([
            'imei_1' => '',
        ]);

        $this->invoice->update([
            'product_description' => '',
        ]);

        $this->device->refresh();

        $deviceImei1SimilarityValidator = new DeviceImei1ValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceImei1SimilarityValidator->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 0);
        $this->assertEquals($result->min_similarity_ratio, 90);
    }
}
