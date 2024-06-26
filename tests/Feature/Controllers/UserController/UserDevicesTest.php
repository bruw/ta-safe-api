<?php

namespace Tests\Feature\Controllers\UserController;

use App\Http\Messages\FlashMessage;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserDevicesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $user2;
    private DeviceModel $deviceModel1;
    private DeviceModel $deviceModel2;
    private Device $device1;
    private Device $device2;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->user = User::factory()->create();
        $this->user2 = User::factory()->create();

        $this->deviceModel1 = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->deviceModel2 = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device1 = Device::factory()
            ->for($this->user)
            ->for($this->deviceModel1)
            ->has(Invoice::factory())
            ->create();

        $this->device2 = Device::factory()
            ->for($this->user)
            ->for($this->deviceModel2)
            ->has(Invoice::factory())
            ->create();

        $this->device1->refresh();
        $this->device2->refresh();
    }

    public function test_an_unauthenticated_user_must_not_be_authorized_to_view_devices(): void
    {
        $response = $this->getJson('/api/user/devices');

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_an_authenticated_user_must_be_authorized_to_view_your_devices(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/user/devices');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) => $json->has(2)
                    ->first(
                        fn (AssertableJson $json) => $json->where('id', $this->device1->id)
                            ->where('color', $this->device1->color)
                            ->where('imei_1', $this->device1->imei_1)
                            ->where('imei_2', $this->device1->imei_2)
                            ->where('access_key', $this->device1->invoice->access_key)
                            ->where('validation_status', $this->device1->validation_status->value)
                            ->where('sharing_token', null)
                            ->has('created_at')
                            ->has('updated_at')
                            ->where('user.id', $this->user->id)
                            ->where('user.name', $this->user->name)
                            ->where('user.cpf', $this->user->cpf)
                            ->where('user.phone', $this->user->phone)
                            ->has('user.created_at')
                            ->has('user.updated_at')
                            ->missing('user.password')
                            ->where('device_model.name', $this->deviceModel1->name)
                            ->where('device_model.ram', $this->deviceModel1->ram)
                            ->where('device_model.storage', $this->deviceModel1->storage)
                            ->where('device_model.brand.name', $this->deviceModel1->brand->name)
                    )

            );
    }

    public function test_a_user_with_no_registered_devices_should_receive_an_empty_collection(): void
    {
        $user2 = User::factory()->create();

        Sanctum::actingAs($user2);

        $response = $this->getJson('/api/user/devices');

        $response->assertOk();
        $this->assertEmpty($response->getData());
    }
}
