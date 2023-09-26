<?php

namespace Tests\Feature\Controllers\DeviceController;

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

class ViewDeviceTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->owner = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device = Device::factory()
            ->for($this->owner)
            ->for($deviceModel)
            ->has(Invoice::factory())
            ->create();

        $this->device->refresh();
    }

    public function test_an_unauthenticated_user_should_not_be_allowed_to_view_a_device(): void
    {
        $response = $this->getJson("/api/devices/{$this->device->id}");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_the_owner_user_must_be_authorized_to_view_their_device(): void
    {
        Sanctum::actingAs(
            $this->owner,
            []
        );

        $response = $this->getJson("/api/devices/{$this->device->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('id', $this->device->id)
                    ->where('color', $this->device->color)
                    ->where('imei_1', $this->device->imei_1)
                    ->where('imei_2', $this->device->imei_2)
                    ->where('validation_status', $this->device->validation_status->value)
                    ->has('created_at')
                    ->has('updated_at')
                    ->where('user.id', $this->owner->id)
                    ->where('user.name', $this->owner->name)
                    ->where('user.cpf', $this->owner->cpf)
                    ->where('user.phone', $this->owner->phone)
                    ->has('user.created_at')
                    ->has('user.updated_at')
                    ->missing('user.password')
                    ->where('deviceModel.name', $this->device->deviceModel->name)
                    ->where('deviceModel.ram', $this->device->deviceModel->ram)
                    ->where('deviceModel.storage', $this->device->deviceModel->storage)
                    ->has('deviceModel.created_at')
                    ->has('deviceModel.updated_at')
                    ->where('deviceModel.brand.name', $this->device->deviceModel->brand->name)
                    ->has('deviceModel.brand.created_at')
                    ->has('deviceModel.brand.updated_at')
                    ->etc()
            );
    }

    public function test_the_user_who_is_not_the_owner_of_the_device_should_not_be_allowed_to_view_it(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user,
            []
        );

        $response = $this->getJson("/api/devices/{$this->device->id}");

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthorized'))
                    ->etc()
            );
    }
}