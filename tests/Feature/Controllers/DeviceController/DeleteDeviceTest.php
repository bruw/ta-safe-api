<?php

namespace Tests\Feature\Controllers\DeviceControllers;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use App\Models\Device;
use App\Models\DeviceModel;
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

class DeleteDeviceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private DeviceModel $deviceModel;
    private Device $device;


    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->deviceModel = DeviceModelFactory::new()
            ->for(BrandFactory::new()->create())
            ->create();

        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::REJECTED
            ]);
    }

    public function test_an_unauthenticated_user_should_not_be_authorized_to_delete_a_device(): void
    {
        $response = $this->deleteJson("api/devices/{$this->device->id}");

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_a_user_should_be_authorized_to_delete_their_invalid_devices(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("api/devices/{$this->device->id}");

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans('actions.device.deleted'))
        );
    }

    public function test_a_user_should_not_be_authorized_to_delete_another_user_devices(): void
    {
        Sanctum::actingAs(UserFactory::new()->create());

        $response = $this->deleteJson("api/devices/{$this->device->id}");

        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthorized'))
        );
    }
}
