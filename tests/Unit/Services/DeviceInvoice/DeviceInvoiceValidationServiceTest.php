<?php

namespace Tests\Unit\Services\DeviceInvoice;

use App\Models\DeviceAttributeValidationLog;
use App\Services\DeviceInvoice\DeviceInvoiceValidationService;

class DeviceInvoiceValidationServiceTest extends DeviceInvoiceValidationServiceTestSetUp
{
    public function test_the_service_must_return_an_instance_of_user_cpf_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $userCpfValidationLog = $service->validateOwnerCpf();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $userCpfValidationLog);
        $this->assertEquals($userCpfValidationLog->attribute_source, 'App\Models\User');
        $this->assertEquals($userCpfValidationLog->attribute_label, 'cpf');
    }

    public function test_the_service_must_return_an_instance_of_user_name_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $userNameValidationLog = $service->validateOwnerName();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $userNameValidationLog);
        $this->assertEquals($userNameValidationLog->attribute_source, 'App\Models\User');
        $this->assertEquals($userNameValidationLog->attribute_label, 'user_name');
    }

    public function test_the_service_must_return_an_instance_of_device_brand_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $deviceBrandValidationLog = $service->validateBrand();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceBrandValidationLog);
        $this->assertEquals($deviceBrandValidationLog->attribute_source, 'App\Models\Brand');
        $this->assertEquals($deviceBrandValidationLog->attribute_label, 'brand_name');
    }

    public function test_the_service_must_return_an_instance_of_device_model_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $deviceModelValidationLog = $service->validateModel();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceModelValidationLog);
        $this->assertEquals($deviceModelValidationLog->attribute_source, 'App\Models\DeviceModel');
        $this->assertEquals($deviceModelValidationLog->attribute_label, 'model_name');
    }

    public function test_the_service_must_return_an_instance_of_device_ram_memory_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $deviceRamValidationLog = $service->validateRam();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceRamValidationLog);
        $this->assertEquals($deviceRamValidationLog->attribute_source, 'App\Models\DeviceModel');
        $this->assertEquals($deviceRamValidationLog->attribute_label, 'ram');
    }

    public function test_the_service_must_return_an_instance_of_device_storage_memory_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $deviceStorageValidationLog = $service->validateStorage();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceStorageValidationLog);
        $this->assertEquals($deviceStorageValidationLog->attribute_source, 'App\Models\DeviceModel');
        $this->assertEquals($deviceStorageValidationLog->attribute_label, 'storage');
    }

    public function test_the_service_must_return_an_instance_of_device_color_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device, $this->invoiceProduct());
        $deviceColorValidationLog = $service->validateColor();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceColorValidationLog);
        $this->assertEquals($deviceColorValidationLog->attribute_source, 'App\Models\Device');
        $this->assertEquals($deviceColorValidationLog->attribute_label, 'color');
    }
}
