<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Ram;

use App\Actions\DeviceInvoice\Validation\Ram\DeviceRamValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\DeviceAttributeValidationLog;
use App\Models\DeviceModel;

class DeviceRamValidationActionTest extends DeviceRamValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceRamValidationAction($this->device, $this->invoiceProduct()))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceRamValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'ram',
            'attribute_value' => $this->device->deviceModel->ram,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct(),
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_RAM_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_identical_device_model_ram_values_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceRamValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_the_action_must_not_validate_a_device_model_ram_with_a_similarity_ratio_of_less_than_70_points(): void
    {
        $log = (new DeviceRamValidationAction(
            device: $this->device,
            invoiceProduct: $this->invoiceProduct('250 Gb')
        ))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(16, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }
}
