<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\Validate;

use App\Actions\DeviceInvoice\Validation\Validate\DeviceValidationAction;
use App\Enums\Device\DeviceValidationStatus;
use Illuminate\Support\Str;

class DeviceInvoiceValidateActionTest extends DeviceInvoiceValidateActionTestSetUp
{
    public function test_should_validate_a_device_registration_when_the_mandatory_attributes_have_been_successfully_validated(): void
    {
        $device = (new DeviceValidationAction($this->device))->execute();
        $this->assertEquals(DeviceValidationStatus::VALIDATED, $device->validation_status);
    }

    public function test_should_rejected_a_device_registration_when_the_user_cpf_is_not_equal_to_the_invoice_consumer_cpf(): void
    {
        $this->device->invoice->update(['consumer_cpf' => '000.000.000-00']);
        $device = (new DeviceValidationAction($this->device))->execute();

        $this->assertEquals(DeviceValidationStatus::REJECTED, $device->validation_status);
    }

    public function test_should_rejected_a_device_registration_when_the_user_name_is_not_equal_to_the_invoice_consumer_name(): void
    {
        $this->device->invoice->update(['consumer_name' => Str::random(10)]);
        $device = (new DeviceValidationAction($this->device))->execute();

        $this->assertEquals(DeviceValidationStatus::REJECTED, $device->validation_status);
    }

    public function test_should_rejected_a_device_registration_when_the_brand_name_is_not_validated(): void
    {
        $this->device->deviceModel->brand->update(['name' => Str::random(10)]);
        $device = (new DeviceValidationAction($this->device))->execute();

        $this->assertEquals(DeviceValidationStatus::REJECTED, $device->validation_status);
    }

    public function test_should_rejected_a_device_registration_when_the_device_model_name_is_not_validated(): void
    {
        $this->device->deviceModel->update(['name' => Str::random(10)]);
        $device = (new DeviceValidationAction($this->device))->execute();

        $this->assertEquals(DeviceValidationStatus::REJECTED, $device->validation_status);
    }

    public function test_should_rejected_a_device_registration_when_the_device_model_ram_and_model_storage_is_not_validated(): void
    {
        $this->device->deviceModel->update(['ram' => Str::random(10), 'storage' => Str::random(10)]);
        $device = (new DeviceValidationAction($this->device))->execute();

        $this->assertEquals(DeviceValidationStatus::REJECTED, $device->validation_status);
    }
}
