<?php

namespace Tests\Unit\Jobs\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Jobs\Device\ValidateDeviceRegistrationJob;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\StringNormalizer;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidateDeviceRegistrationJobTest extends TestCase
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
        $this->user = UserFactory::new()->create([
            'name' => 'Dr. Hans Chucrute',
        ]);
    }

    private function brandSetUp(): void
    {
        $this->brand = BrandFactory::new()->create([
            'name' => 'Xiaomi',
        ]);
    }

    private function deviceSetUp(): void
    {
        $this->deviceModel = DeviceModelFactory::new()
            ->for($this->brand)
            ->create([
                'name' => 'Poco x5 Pro',
            ]);

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

    public function test_the_job_should_validate_a_device_registration_when_the_critical_attributes_have_been_successfuly_validated(): void
    {
        $ValidateDeviceJob = new ValidateDeviceRegistrationJob($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $ValidateDeviceJob->handle($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::VALIDATED
        );
    }

    public function test_the_job_must_rejected_a_device_registration_when_the_user_cpf_is_not_validated(): void
    {
        $this->user->update([
            'cpf' => '000.000.000-00',
        ]);

        $ValidateDeviceJob = new ValidateDeviceRegistrationJob($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $ValidateDeviceJob->handle($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );
    }

    public function test_the_job_must_rejected_a_device_registration_when_the_user_name_is_not_validated(): void
    {
        $this->user->update([
            'name' => 'Chucrute',
        ]);

        $ValidateDeviceJob = new ValidateDeviceRegistrationJob($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $ValidateDeviceJob->handle($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );
    }

    public function test_the_job_must_rejected_a_device_registration_when_the_brand_name_is_not_validated(): void
    {
        $this->brand->update([
            'name' => 'Samsung',
        ]);

        $ValidateDeviceJob = new ValidateDeviceRegistrationJob($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $ValidateDeviceJob->handle($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );
    }

    public function test_the_job_must_rejected_a_device_registration_when_the_device_model_name_is_not_validated(): void
    {
        $this->deviceModel->update([
            'name' => 'Poco x4',
        ]);

        $this->device->refresh();

        $ValidateDeviceJob = new ValidateDeviceRegistrationJob($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $ValidateDeviceJob->handle($this->device);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );
    }
}
