<?php

namespace Tests\Unit\Actions\DeviceInvoiceProductValidation;

use App\Actions\DeviceInvoiceProductValidation\DeviceStorageValidationAction;
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

class DeviceStorageValidationTest extends TestCase
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
                'storage' => '512 GB',
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

    public function test_must_validate_identical_device_storage_size(): void
    {
        $deviceStorageSimilarity = new DeviceStorageValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceStorageSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_record_in_the_database_for_successful_validations(): void
    {
        $deviceStorageSimilarity = new DeviceStorageValidationAction(
            $this->device, $this->invoice->product_description
        );

        $deviceStorageSimilarity->execute();

        $deviceModelStorage = $this->normalizeMemorySize(
            $this->device->deviceModel->storage
        );

        $product = $this->normalizeMemorySize(
            $this->invoice->product_description
        );

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'storage',
            'attribute_value' => $deviceModelStorage,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $product,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 70,
            'validated' => true,
        ]);
    }

    public function test_must_be_able_to_validate_a_device_model_storage_even_if_it_is_between_a_text(): void
    {
        $this->invoice->update([
            'product_description' => "Smartphone Samsung Galaxy s23 Ultra 5G, {$this->device->deviceModel->storage}/8gb, Tela de 6.9 polegadas",
        ]);

        $deviceStorageSimilarity = new DeviceStorageValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceStorageSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 70);
    }

    public function test_the_action_must_validate_a_device_model_storage_with_a_similarity_ratio_greater_than_or_equal_70_porcent(): void
    {
        $this->deviceModel->update([
            'storage' => '128 gb',
        ]);

        $this->invoice->update([
            'product_description' => '128 GB Armaz. 8 Gb ram',
        ]);

        $this->device->refresh();

        $deviceStorageSimilarity = new DeviceStorageValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceStorageSimilarity->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 70);
    }

    public function test_the_action_must_not_validate_a_device_model_storage_with_a_similarity_ratio_of_less_than_70_porcent(): void
    {
        $this->deviceModel->update([
            'storage' => '20 GB',
        ]);

        $this->invoice->update([
            'product_description' => '20 Armazenamento, 16GB Ram',
        ]);

        $this->device->refresh();

        $deviceStorageSimilarity = new DeviceStorageValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $deviceStorageSimilarity->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 13);
        $this->assertEquals($result->min_similarity_ratio, 70);
    }
}
