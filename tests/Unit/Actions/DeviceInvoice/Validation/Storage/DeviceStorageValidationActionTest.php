<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Storage;

use App\Actions\DeviceInvoice\Validation\Storage\DeviceStorageValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\DeviceAttributeValidationLog;
use App\Models\DeviceModel;

class DeviceStorageValidationActionTest extends DeviceStorageValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceStorageValidationAction($this->device, $this->invoiceProduct()))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceStorageValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'storage',
            'attribute_value' => $this->device->deviceModel->storage,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct(),
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_STORAGE_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_identical_device_model_ram_values_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceStorageValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_the_action_must_not_validate_a_device_model_ram_with_a_similarity_ratio_of_less_than_70_points(): void
    {
        $log = (new DeviceStorageValidationAction(
            device: $this->device,
            invoiceProduct: $this->invoiceProduct('10gb')
        ))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(13, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }

    public function test_should_invalidate_the_device_model_storage_when_an_empty_string_is_provided(): void
    {
        $log = (new DeviceStorageValidationAction($this->device, ''))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(0, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }
}
