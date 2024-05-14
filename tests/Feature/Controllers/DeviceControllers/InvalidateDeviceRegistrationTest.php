<?php

namespace Tests\Feature\Controllers\DeviceController;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use App\Models\Device;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InvalidateDeviceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
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

    private function deviceSetUp(): void
    {
        $deviceModel = DeviceModelFactory::new()
            ->for(BrandFactory::new()->create())
            ->create();

        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($deviceModel)
            ->create();
    }

    private function invoiceSetUp(): void
    {
        InvoiceFactory::new()
            ->for($this->device)
            ->create();
    }

    private function validateDeviceRoute($deviceId): string
    {
        return "api/devices/{$deviceId}/invalidate";
    }

    /*
    ================= **START OF TESTS** ==========================================================================
    */

    public function test_an_unauthenticated_user_should_not_be_allowed_to_invalidate_a_device_registration(): void
    {
        $route = $this->validateDeviceRoute($this->device->id);
        $response = $this->postJson($route);

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_user_who_owns_the_device_must_be_able_to_invalidate_the_registration(): void
    {
        Sanctum::actingAs($this->user);

        $route = $this->validateDeviceRoute($this->device->id);
        $response = $this->postJson($route);

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans('actions.device_validation.invalid'))
                ->where('device.id', $this->device->id)
                ->where('device.color', $this->device->color)
                ->where('device.imei_1', $this->device->imei_1)
                ->where('device.imei_2', $this->device->imei_2)
                ->where('device.access_key', $this->device->invoice->access_key)
                ->where('device.validation_status', DeviceValidationStatus::REJECTED->value)
                ->has('device.sharing_token')
                ->has('device.created_at')
                ->has('device.updated_at')
                ->has('device.user')
                ->has('device.device_model')
                ->has('device.device_model.brand')
                ->etc()
        );
    }

    public function test_the_user_who_does_not_own_the_device_must_not_be_able_to_invalidate_the_registration(): void
    {
        Sanctum::actingAs(UserFactory::new()->create());

        $route = $this->validateDeviceRoute($this->device->id);
        $response = $this->postJson($route);

        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthorized'))
        );
    }
}
