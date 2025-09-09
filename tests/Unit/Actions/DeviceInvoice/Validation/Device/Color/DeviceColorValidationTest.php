<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Device\Color;

use App\Actions\DeviceInvoice\Validation\Device\Color\DeviceColorValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;

class DeviceColorValidationTest extends DeviceColorValidationTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceColorValidationAction($this->device, $this->invoiceProduct()))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceColorValidationAction($this->device, $this->invoiceProduct()))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => Device::class,
            'attribute_label' => 'color',
            'attribute_value' => $this->device->color,
            'invoice_attribute_label' => 'product_description',
            'invoice_attribute_value' => $this->invoiceProduct(),
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_COLOR_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_the_action_must_validate_a_device_color_with_a_similarity_ratio_greater_than_or_equal_70_porcent(): void
    {
        $log = (new DeviceColorValidationAction($this->device, 'Azulado'))->execute();

        $this->assertTrue($log->validated);
        $this->assertEquals(72, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }

    public function test_the_action_must_not_validate_a_device_color_with_a_similarity_ratio_of_less_than_70_porcent(): void
    {
        $log = (new DeviceColorValidationAction($this->device, 'Azulado Verde'))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(47, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }

    public function test_should_validate_similar_device_color_even_if_they_have_accents_or_other_special_characters(): void
    {
        $this->device->update(['color' => 'Azul Petróleo']);
        $log = (new DeviceColorValidationAction($this->device, 'Ázul (pêtróleo!)'))->execute();

        $this->assertTrue($log->validated);
        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }

    public function test_should_invalidate_the_color_attribute_when_an_empty_string_is_provided_as_the_invoice_product(): void
    {
        $log = (new DeviceColorValidationAction($this->device, ''))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(0, $log->similarity_ratio);
        $this->assertEquals(70, $log->min_similarity_ratio);
    }
}
