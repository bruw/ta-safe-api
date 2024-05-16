<?php

namespace Tests\Unit\Actions\DeviceInvoiceProductValidation;

use App\Actions\DeviceInvoiceProductValidation\DeviceBrandValidationAction;
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

class DeviceBrandValidationTest extends TestCase
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
                . " {$this->device->deviceModel->name}"
                . " {$this->device->deviceModel->ram}"
                . " {$this->device->deviceModel->storage}"
                . " {$this->device->color}",
            ]);
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_must_validate_identical_brands(): void
    {
        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $brandSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_record_in_the_database_for_successful_validations(): void
    {
        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $brandSimilarityValidator->execute();

        $brand = $this->extractOnlyLetters($this->brand->name);
        $products = $this->extractOnlyLetters($this->invoice->product_description);

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Brand::class,
            'attribute_label' => 'brand_name',
            'attribute_value' => $brand,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $products,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 75,
            'validated' => true,
        ]);
    }

    public function test_must_be_able_to_validate_a_brand_even_if_it_is_in_the_middle_of_a_text(): void
    {
        $this->invoice->update([
            'product_description' => "Smartphone {$this->brand->name} Galaxy S20 Ultra 5G, 128GB, Cosmic Gray, 12GB RAM, Tela de 6.9 polegadas",
        ]);

        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $brandSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_the_action_must_validate_with_a_similarity_ratio_of_more_than_80_porcent(): void
    {
        $this->brand->update([
            'name' => 'Xiaomi',
        ]);

        $this->invoice->update([
            'product_description' => 'Xiaoni',
        ]);

        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $brandSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 83);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_the_should_not_validate_brands_with_a_similarity_ratio_of_less_than_80_percent(): void
    {
        $this->brand->update([
            'name' => 'Xiaomi',
        ]);

        $this->invoice->update([
            'product_description' => 'Xiaon',
        ]);

        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $brandSimilarityValidator->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 72);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_should_validate_similar_brands_even_if_they_have_accents_or_other_special_characters(): void
    {
        $this->brand->update([
            'name' => 'Apple',
        ]);

        $this->invoice->update([
            'product_description' => 'Âpple',
        ]);

        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $brandSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_should_not_validate_brands_that_are_empty_strings(): void
    {
        $this->brand->update([
            'name' => '',
        ]);

        $this->invoice->update([
            'product_description' => '',
        ]);

        $brandSimilarityValidator = new DeviceBrandValidationAction(
            $this->device, $this->invoice->product_description
        );

        $result = $brandSimilarityValidator->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 0);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

}
