<?php

namespace Tests\Unit\Services\DeviceRegisterValidations;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceAttributeValidationLog;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use App\Services\DeviceRegisterValidations\DeviceInvoiceValidationService;
use App\Traits\StringNormalizer;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceInvoiceValidationServiceTest extends TestCase
{
    use RefreshDatabase;
    use StringNormalizer;

    private User $user;
    private Brand $brand;
    private DeviceModel $deviceModel;
    private Device $device;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->brandSetUp();
        $this->deviceSetUp();
        $this->invoiceSetUp();
    }

    /*
    ================= **START OF SETUP** ==========================================================================
    */

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

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
            ->for($this->user)
            ->for($this->deviceModel)
            ->create();
    }

    private function invoiceSetUp(): void
    {
        $this->invoice = InvoiceFactory::new()
            ->for($this->device)
            ->create([
                'consumer_cpf' => $this->user->cpf,
                'consumer_name' => $this->user->name,
                'product_description' => "<span>{$this->brand->name}"
                . " {$this->deviceModel->name}"
                . " {$this->device->deviceModel->ram}"
                . " {$this->device->deviceModel->storage}"
                . " {$this->device->color}</span>",
            ]);
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_the_service_must_return_an_instance_of_user_cpf_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $userCpfValidationLog = $service->validateOwnerCpf();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $userCpfValidationLog);
        $this->assertEquals($userCpfValidationLog->attribute_source, 'App\Models\User');
        $this->assertEquals($userCpfValidationLog->attribute_label, 'cpf');
    }

    public function test_the_service_must_return_an_instance_of_user_name_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $userNameValidationLog = $service->validateOwnerName();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $userNameValidationLog);
        $this->assertEquals($userNameValidationLog->attribute_source, 'App\Models\User');
        $this->assertEquals($userNameValidationLog->attribute_label, 'name');
    }

    public function test_the_service_must_return_an_instance_of_device_brand_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);

        $deviceBrandValidationLog = $service->validateBrand();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceBrandValidationLog);
        $this->assertEquals($deviceBrandValidationLog->attribute_source, 'App\Models\Brand');
        $this->assertEquals($deviceBrandValidationLog->attribute_label, 'name');
    }

    public function test_the_service_must_return_an_instance_of_device_model_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $deviceModelValidationLog = $service->validateModel();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceModelValidationLog);
        $this->assertEquals($deviceModelValidationLog->attribute_source, 'App\Models\DeviceModel');
        $this->assertEquals($deviceModelValidationLog->attribute_label, 'name');
    }

    public function test_the_service_must_return_an_instance_of_device_ram_memory_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $deviceRamValidationLog = $service->validateRam();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceRamValidationLog);
        $this->assertEquals($deviceRamValidationLog->attribute_source, 'App\Models\DeviceModel');
        $this->assertEquals($deviceRamValidationLog->attribute_label, 'ram');
    }

    public function test_the_service_must_return_an_instance_of_device_storage_memory_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $deviceStorageValidationLog = $service->validateStorage();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceStorageValidationLog);
        $this->assertEquals($deviceStorageValidationLog->attribute_source, 'App\Models\DeviceModel');
        $this->assertEquals($deviceStorageValidationLog->attribute_label, 'storage');
    }

    public function test_the_service_must_return_an_instance_of_device_color_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $deviceColorValidationLog = $service->validateColor();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceColorValidationLog);
        $this->assertEquals($deviceColorValidationLog->attribute_source, 'App\Models\Device');
        $this->assertEquals($deviceColorValidationLog->attribute_label, 'color');
    }

    public function test_the_service_must_return_an_instance_of_device_imei1_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $deviceImei1ValidationLog = $service->validateImei1();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceImei1ValidationLog);
        $this->assertEquals($deviceImei1ValidationLog->attribute_source, 'App\Models\Device');
        $this->assertEquals($deviceImei1ValidationLog->attribute_label, 'imei_1');
    }

    public function test_the_service_must_return_an_instance_of_device_imei2_log_validation(): void
    {
        $service = new DeviceInvoiceValidationService($this->device);
        $deviceImei2ValidationLog = $service->validateImei2();

        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $deviceImei2ValidationLog);
        $this->assertEquals($deviceImei2ValidationLog->attribute_source, 'App\Models\Device');
        $this->assertEquals($deviceImei2ValidationLog->attribute_label, 'imei_2');
    }
}
