<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Device\Model;

use App\Actions\DeviceInvoice\Validation\Device\Model\DeviceModelNameValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\DeviceAttributeValidationLog;
use App\Models\DeviceModel;

class DeviceModelNameValidationActionTest extends DeviceModelNameValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceModelNameValidationAction($this->device, $this->invoiceProduct()))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceModelNameValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => DeviceModel::class,
            'attribute_label' => 'model_name',
            'attribute_value' => $this->device->deviceModel->name,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct(),
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_MODEL_NAME_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_identical_model_names_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceModelNameValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_the_action_must_not_validate_a_device_model_name_with_a_similarity_ratio_of_less_than_85_points(): void
    {
        $this->device->deviceModel->update(['name' => 'Poco x4 Pro']);
        $log = (new DeviceModelNameValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(84, $log->similarity_ratio);
        $this->assertEquals(85, $log->min_similarity_ratio);
    }

    public function test_the_action_must_validate_a_device_model_name_with_a_similarity_ratio_greater_than_or_equal_85_points(): void
    {
        $log = (new DeviceModelNameValidationAction(
            device: $this->device,
            invoiceProduct: 'Poc x5 Pr'
        ))->execute();

        $this->assertTrue($log->validated);
        $this->assertEquals(90, $log->similarity_ratio);
        $this->assertEquals(85, $log->min_similarity_ratio);
    }

    public function test_should_validate_similar_device_model_names_even_if_they_have_accents_or_other_special_characters(): void
    {
        $log = (new DeviceModelNameValidationAction(
            device: $this->device,
            invoiceProduct: 'Pôco x5 Pró!'
        ))->execute();

        $this->assertTrue($log->validated);
        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertEquals(85, $log->min_similarity_ratio);
    }

    public function test_should_invalidate_the_device_model_name_attribute_when_an_empty_string_is_provided(): void
    {
        $log = (new DeviceModelNameValidationAction($this->device, ''))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals($log->similarity_ratio, 0);
        $this->assertEquals($log->min_similarity_ratio, 85);
    }
}
