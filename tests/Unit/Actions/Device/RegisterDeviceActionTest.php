<?php

namespace Tests\Unit\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterDeviceActionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private array $data;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->user = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $invoice = Invoice::factory()->make();

        $device = Device::factory()
            ->for($this->user)
            ->for($deviceModel)
            ->make();

        $this->data = [
            'device_model_id' => $deviceModel->id,
            'color' => $device->color,
            'imei_1' => $device->imei_1,
            'imei_2' => $device->imei_2,
            'access_key' => $invoice->access_key
        ];
    }

    public function test_should_return_true_if_the_device_is_successfully_registered(): void
    {
        $this->assertTrue(
            $this->user->registerDevice($this->data)
        );
    }

    public function test_should_increase_the_number_of_devices_linked_to_the_user(): void
    {
        $userDevicesCount = $this->user->devices->count();
        $this->assertEquals($userDevicesCount, 0);

        $this->user->registerDevice($this->data);
        $this->user->refresh();

        $userDevicesCount = $this->user->devices->count();
        $this->assertEquals($userDevicesCount, 1);
    }

    public function test_must_corretcly_register_the_device_params(): void
    {
        $this->user->registerDevice($this->data);
        $this->user->refresh();

        $device = $this->user->devices->first();

        $this->assertEquals(
            $device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $this->assertEquals(
            $device->deviceModel->id,
            $this->data['device_model_id']
        );

        $this->assertEquals(
            $device->color,
            $this->data['color']
        );

        $this->assertEquals(
            $device->imei_1,
            $this->data['imei_1']
        );

        $this->assertEquals(
            $device->imei_2,
            $this->data['imei_2']
        );
    }

    public function test_must_corretcly_record_of_the_params_of_the_device_invoice(): void
    {
        $this->user->registerDevice($this->data);
        $this->user->refresh();

        $device = $this->user->devices->first();

        $this->assertEquals(
            $device->invoice->access_key,
            $this->data['access_key']
        );

        $this->assertNull($device->invoice->consumer_cpf);
        $this->assertNull($device->invoice->consumer_name);
        $this->assertNull($device->invoice->consumer_description);
    }

    public function test_should_return_an_exception_and_not_register_the_device_if_an_internal_error_occurs(): void
    {
        $userDevicesCount = $this->user->devices->count();
        $this->assertEquals($userDevicesCount, 0);

        $this->data['device_model_id'] = Str::random(10);
        $exceptionOcurred = false;

        try {
            $this->user->registerDevice($this->data);
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_registration.unable_to_register_device')
            );

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $this->assertTrue($exceptionOcurred);

        $userDevicesCount = $this->user->devices->count();
        $this->assertEquals($userDevicesCount, 0);
    }
}
