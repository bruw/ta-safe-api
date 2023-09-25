<?php

namespace Tests\Feature\Controllers\UserController;

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

class GetUserDevicesTest extends TestCase
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
        $response = $this->getJson("/api/users/{$this->user->id}/devices");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_an_authenticated_user_must_be_authorized_to_view_your_devices(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->getJson("/api/users/{$this->user->id}/devices");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(2)
                    ->first(
                        fn (AssertableJson $json) =>
                        $json->where('id', $this->device2->id)
                            ->where('color', $this->device2->color)
                            ->where('imei_1', $this->device2->imei_1)
                            ->where('imei_2', $this->device2->imei_2)
                            ->where('validation_status', $this->device1->validation_status->value)
                            ->has('created_at')
                            ->has('updated_at')
                            ->where('user.id', $this->user->id)
                            ->where('user.name', $this->user->name)
                            ->where('user.cpf', $this->user->cpf)
                            ->where('user.phone', $this->user->phone)
                            ->has('user.created_at')
                            ->has('user.updated_at')
                            ->missing('user.password')
                            ->where('deviceModel.name', $this->deviceModel2->name)
                            ->where('deviceModel.ram', $this->deviceModel2->ram)
                            ->where('deviceModel.storage', $this->deviceModel2->storage)
                            ->has('deviceModel.created_at')
                            ->has('deviceModel.updated_at')
                            ->where('deviceModel.brand.name', $this->deviceModel2->brand->name)
                            ->has('deviceModel.brand.created_at')
                            ->has('deviceModel.brand.updated_at')
                            ->etc()
                    )

            );
    }

    public function test_a_user_should_not_be_allowed_to_view_another_user_devices(): void
    {
        Sanctum::actingAs(
            $this->user2,
            []
        );

        $response = $this->getJson("/api/users/{$this->user->id}/devices");

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthorized'))
                    ->etc()
            );
    }

    public function test_a_user_with_no_registered_devices_should_receive_an_empty_collection(): void
    {
        $user2 = User::factory()->create();

        Sanctum::actingAs(
            $user2,
            []
        );

        $response = $this->getJson("/api/users/{$user2->id}/devices");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEmpty($response->getData());
    }
}
