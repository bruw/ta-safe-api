<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Device\Brand;

use App\Actions\DeviceInvoice\Validation\Device\Brand\DeviceBrandValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Brand;
use App\Models\DeviceAttributeValidationLog;

class DeviceBrandValidationActionTest extends DeviceBrandValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceBrandValidationAction($this->device, $this->invoiceProduct()))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceBrandValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Brand::class,
            'attribute_label' => 'brand_name',
            'attribute_value' => $this->brand->name,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct(),
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_BRAND_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_identical_brands_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceBrandValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_similarity_score_below_75_points_should_be_invalidated(): void
    {
        $similarBrand = $this->brand->name . 'e';
        $log = (new DeviceBrandValidationAction($this->device, $this->invoiceProduct($similarBrand)))->execute();

        $this->assertEquals(24, $log->similarity_ratio);
        $this->assertFalse($log->validated);
    }

    public function test_should_invalidate_the_brand_attribute_when_an_empty_string_is_provided_as_the_invoice_product(): void
    {
        $log = (new DeviceBrandValidationAction($this->device, ''))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(0, $log->similarity_ratio);
        $this->assertEquals(75, $log->min_similarity_ratio);
    }
}
