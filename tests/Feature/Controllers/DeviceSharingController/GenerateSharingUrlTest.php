<?php

namespace Tests\Feature\Controllers\DeviceSharingController;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\StringMasks;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class GenerateSharingUrlTest extends TestCase
{
    use RefreshDatabase;
    use StringMasks;

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

    public function test_an_unauthenticated_user_should_not_be_allowed_to_generate_the_device_data_sharing_url(): void
    {
        $response = $this->postJson("/api/devices/{$this->device->id}/share");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_the_owner_user_must_be_authorized_to_generate_the_device_data_sharing_url(): void
    {
        Sanctum::actingAs(
            $this->owner,
            []
        );

        $response = $this->postJson("/api/devices/{$this->device->id}/share");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('url')
                    ->etc()
            );
    }

    public function test_a_user_should_not_be_authorized_to_generate_data_sharing_url_for_devices_that_do_not_belongs_to_them(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user,
            []
        );

        $response = $this->postJson("/api/devices/{$this->device->id}/share");

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthorized'))
                    ->etc()
            );
    }

    public function test_the_generated_url_must_return_the_device_registration_data(): void
    {
        Sanctum::actingAs(
            $this->owner,
            []
        );

        $response = $this->postJson("/api/devices/{$this->device->id}/share");
        $response->assertOk();

        $url = $response->getData()->url;

        $response = $this->getJson($url);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(8)
                    ->where('color', $this->device->color)
                    ->where('imei_1', self::addAsteriskMaskForImei($this->device->imei_1))
                    ->where('imei_2', self::addAsteriskMaskForImei($this->device->imei_2))
                    ->where('validation_status', $this->device->validation_status->value)
                    ->has('created_at')
                    ->has('updated_at')
                    ->where('user.name', $this->owner->name)
                    ->where('user.cpf', self::addAsteriskMaskForCpf($this->owner->cpf))
                    ->where('user.phone', self::addAsteriskMaskForPhone($this->owner->phone))
                    ->has('user.created_at')
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

    public function test_the_generated_url_should_expire_after_one_hour(): void
    {
        Sanctum::actingAs(
            $this->owner,
            []
        );

        $response = $this->postJson("/api/devices/{$this->device->id}/share");
        $response->assertOk();

        $url = $response->getData()->url;

        $response = $this->getJson($url);
        $response->assertStatus(Response::HTTP_OK);

        Carbon::setTestNow(Carbon::now()->addHour()->addSecond());

        $response = $this->getJson($url);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
