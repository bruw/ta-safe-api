<?php

namespace Tests\Unit\Actions\DeviceInvoiceProductValidation;

use App\Actions\DeviceInvoiceProductValidation\DeviceRamValidationAction;
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

class DeviceRamValidationTest extends TestCase
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
            ->create([
                'ram' => '8 GB',
            ]);

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

    public function test_must_validate_identical_device_color(): void
    {
        $deviceRamSimilarity = new DeviceRamValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceRamSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_record_in_the_database_for_successful_validations(): void
    {
        $deviceRamSimilarity = new DeviceRamValidationAction(
            $this->device, $this->invoice->product_description
        );

        $deviceRamSimilarity->execute();

        $deviceModelRam = $this->normalizeMemorySize(
            $this->device->deviceModel->ram
        );

        $product = $this->normalizeMemorySize(
            $this->invoice->product_description
        );

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'ram',
            'attribute_value' => $deviceModelRam,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $product,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 70,
            'validated' => true,
        ]);
    }

    public function test_must_be_able_to_validate_a_device_model_ram_even_if_it_is_between_a_text(): void
    {
        $this->invoice->update([
            'product_description' => "Smartphone Samsung Galaxy s23 Ultra 5G, 128GB/{$this->device->deviceModel->ram}, Tela de 6.9 polegadas",
        ]);

        $deviceRamSimilarity = new DeviceRamValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceRamSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 70);
    }

    public function test_the_action_must_validate_a_device_model_ram_with_a_similarity_ratio_greater_than_or_equal_70_porcent(): void
    {
        $this->deviceModel->update([
            'ram' => '8 gb',
        ]);

        $this->invoice->update([
            'product_description' => '256GB Armazenamento, 8 gB ram',
        ]);

        $this->device->refresh();

        $deviceRamSimilarity = new DeviceRamValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceRamSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 70);
    }

    public function test_the_action_must_not_validate_a_device_model_ram_with_a_similarity_ratio_of_less_than_70_porcent(): void
    {
        $this->deviceModel->update([
            'ram' => '16 GB',
        ]);

        $this->invoice->update([
            'product_description' => '256GB Armazenamento, 16 Ram',
        ]);

        $this->device->refresh();

        $deviceRamSimilarity = new DeviceRamValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceRamSimilarity->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 26);
        $this->assertEquals($result->min_similarity_ratio, 70);
    }
}
