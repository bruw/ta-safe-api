<?php

namespace Tests\Feature\Controllers\DeviceSharingController;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;

use App\Traits\StringMasks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ViewDeviceByTokenTest extends TestCase
{
    use RefreshDatabase;
    use StringMasks;

    private User $owner;
    private User $user;
    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->owner = User::factory()->create();
        $this->user = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device = Device::factory()
            ->for($this->owner)
            ->for($deviceModel)
            ->has(Invoice::factory())
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED
            ]);

        $this->device->createSharingToken();

        $this->device->refresh();
    }

    public function test_an_unauthenticated_user_must_not_be_authorized_to_view_a_device(): void
    {
        $response = $this->getJson("/api/devices", [
            'token' => $this->device->sharingToken->token
        ]);

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_an_authenticated_user_with_a_valid_token_must_be_authorized_to_view_a_device(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(
            "/api/devices?token={$this->device->sharingToken->token}"
        );

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('color', $this->device->color)
                    ->where('imei_1', self::addAsteriskMaskForImei($this->device->imei_1))
                    ->where('imei_2', self::addAsteriskMaskForImei($this->device->imei_2))
                    ->where('validation_status', $this->device->validation_status->value)
                    ->has('created_at')
                    ->has('updated_at')
                    ->where('user.id', $this->owner->id)
                    ->where('user.name', $this->owner->name)
                    ->where('user.cpf', self::addAsteriskMaskForCpf($this->owner->cpf))
                    ->where('user.phone', self::addAsteriskMaskForPhone($this->owner->phone))
                    ->has('user.created_at')
                    ->where('device_model.name', $this->device->deviceModel->name)
                    ->where('device_model.ram', $this->device->deviceModel->ram)
                    ->where('device_model.storage', $this->device->deviceModel->storage)
                    ->where('device_model.brand.name', $this->device->deviceModel->brand->name)
                    ->etc()
            );
    }

    public function test_should_return_an_error_when_the_token_param_is_null(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/devices", [
            'token' => null
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.token.0', trans('validation.required', [
                        'attribute' => 'token'
                    ]))
                    ->etc()
            );
    }


    public function test_should_return_an_error_when_the_token_is_not_8_digits(): void
    {
        Sanctum::actingAs($this->user);

        $invalidTokenLength = "123456789";

        $response = $this->getJson("/api/devices?token={$invalidTokenLength}");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.token.0', trans('validation.digits', [
                        'digits' => 8,
                        'attribute' => 'token'
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_erro_when_the_token_not_exists(): void
    {
        Sanctum::actingAs($this->user);

        $invalidToken = "00000000";

        $response = $this->getJson("/api/devices?token={$invalidToken}");

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.token.0', trans('validation.custom.token.exists', [
                        'attribute' => 'token'
                    ]))
                    ->etc()
            );
    }
}
