<?php

namespace Tests\Unit\Actions\DeviceInvoiceProductValidation;

use App\Actions\DeviceInvoiceProductValidation\DeviceModelNameValidationAction;
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

class DeviceModelNameValidationTest extends TestCase
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
            ->create();

        $this->invoice = InvoiceFactory::new()
            ->for($this->device)
            ->create([
                'product_description' => "{$this->brand->name}"
                . " {$this->deviceModel->name}"
                . " {$this->device->deviceModel->ram}"
                . " {$this->device->deviceModel->storage}"
                . " {$this->device->color}",
            ]);
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_must_validate_identical_device_model_names(): void
    {
        $action = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $action->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_record_in_the_database_for_successful_validations(): void
    {
        $deviceNameSimilarity = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $deviceNameSimilarity->execute();

        $deviceName = $this->removeNonAlphanumeric(
            $this->basicNormalize($this->deviceModel->name)
        );

        $product = $this->removeNonAlphanumeric(
            $this->basicNormalize($this->invoice->product_description)
        );

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'name',
            'attribute_value' => $deviceName,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $product,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 85,
            'validated' => true,
        ]);
    }

    public function test_must_be_able_to_validate_a_brand_even_if_it_is_in_the_middle_of_a_text(): void
    {
        $this->invoice->update([
            'product_description' => "Smartphone Motorola {$this->device->deviceModel->name} Ultra 5G, 128GB, Cosmic Gray, 12GB RAM, Tela de 6.9 polegadas",
        ]);

        $deviceNameSimilarity = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceNameSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 85);
    }

    public function test_the_action_must_validate_a_device_model_name_with_a_similarity_ratio_greater_than_or_equal_85_porcent(): void
    {
        $this->deviceModel->update([
            'name' => 'Poco x5 Pro',
        ]);

        $this->invoice->update([
            'product_description' => 'Poc x5 Pr',
        ]);

        $this->device->refresh();

        $deviceNameSimilarity = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceNameSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 90);
        $this->assertEquals($result->min_similarity_ratio, 85);
    }

    public function test_the_action_must_not_validate_a_device_model_name_with_a_similarity_ratio_of_less_than_85_porcent(): void
    {
        $this->deviceModel->update([
            'name' => 'Poco x5 Pro',
        ]);

        $this->invoice->update([
            'product_description' => 'Po x5 Pr',
        ]);

        $this->device->refresh();

        $deviceNameSimilarity = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceNameSimilarity->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 84);
        $this->assertEquals($result->min_similarity_ratio, 85);
    }

    public function test_should_validate_similar_device_model_names_even_if_they_have_accents_or_other_special_characters(): void
    {
        $this->deviceModel->update([
            'name' => 'Galaxy a54.',
        ]);

        $this->invoice->update([
            'product_description' => 'Gâlaxy á54!',
        ]);

        $this->device->refresh();

        $deviceNameSimilarity = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceNameSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 85);
    }

    public function test_should_not_validate_device_model_name_that_are_empty_strings(): void
    {
        $this->deviceModel->update([
            'name' => '',
        ]);

        $this->invoice->update([
            'product_description' => '',
        ]);

        $this->device->refresh();

        $deviceNameSimilarity = new DeviceModelNameValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceNameSimilarity->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 0);
        $this->assertEquals($result->min_similarity_ratio, 85);
    }
}
