<?php

namespace Tests\Feature\Controllers\DeviceSharingController;

use App\Enums\Device\DeviceValidationStatus;

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

class CreateSharingTokenTest extends TestCase
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
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED
            ]);

        $this->device->refresh();
    }

    public function test_an_unauthenticated_user_must_not_be_allowed_to_create_a_device_data_sharing_token(): void
    {
        $response = $this->postJson("/api/devices/{$this->device->id}/share");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_a_user_who_is_not_the_owner_of_the_device_should_not_be_allowed_to_create_a_device_data_sharing_token(): void
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

    public function test_the_owner_user_must_be_authorized_to_generate_a_device_data_sharing_token(): void
    {
        Sanctum::actingAs(
            $this->owner,
            []
        );

        $response = $this->postJson("/api/devices/{$this->device->id}/share");
        $response->assertStatus(Response::HTTP_CREATED);
    }
}
