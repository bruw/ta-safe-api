<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Imei;

use App\Actions\DeviceInvoice\Validation\Imei\DeviceImei1ValidationAction;
use App\Actions\DeviceInvoice\Validation\Imei\DeviceImei2ValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;

class DeviceImei2ValidationActionTest extends DeviceImeiValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceImei2ValidationAction($this->device, $this->invoiceProduct()))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceImei2ValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Device::class,
            'attribute_label' => 'imei_2',
            'attribute_value' => $this->device->imei_2,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct(),
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_IMEI_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_identical_device_imeis_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceImei2ValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_should_invalidate_the_device_imei_1_attribute_when_an_empty_string_is_provided(): void
    {
        $log = (new DeviceImei2ValidationAction($this->device, ''))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(0, $log->similarity_ratio);
        $this->assertEquals(90, $log->min_similarity_ratio);
    }
}
