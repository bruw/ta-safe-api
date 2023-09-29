<?php

namespace Tests\Feature\Controllers\UserController;

use App\Enums\Device\DeviceValidationStatus;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceTransfer;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserDevicesTransfersTest extends TestCase
{
    use RefreshDatabase;

    private User $user1;
    private User $user2;

    private Device $device1;
    private Device $device2;

    private DeviceModel $deviceModel1;
    private DeviceModel $deviceModel2;

    private DeviceTransfer $transfer1;
    private DeviceTransfer $transfer2;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->user1 = User::factory()->create();
        $this->user2 = User::factory()->create();

        $this->deviceModel1 = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->deviceModel2 = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device1 = Device::factory()
            ->for($this->user1)
            ->for($this->deviceModel1)
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED
            ]);

        $this->device2 = Device::factory()
            ->for($this->user2)
            ->for($this->deviceModel2)
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED
            ]);

        $this->transfer1 = DeviceTransfer::create([
            'source_user_id' => $this->user1->id,
            'target_user_id' => $this->user2->id,
            'device_id' => $this->device1->id
        ]);

        $this->transfer2 = DeviceTransfer::create([
            'source_user_id' => $this->user2->id,
            'target_user_id' => $this->user1->id,
            'device_id' => $this->device2->id
        ]);

        $this->user1->refresh();
        $this->user2->refresh();

        $this->device1->refresh();
        $this->device2->refresh();

        $this->transfer1->refresh();
        $this->transfer2->refresh();
    }

    public function test_an_unauthenticated_user_should_not_be_allowed_to_view_devices_transfers(): void
    {
        $response = $this->getJson("/api/user/devices-transfers");

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_an_authenticated_user_must_be_allowed_to_view_the_transfers_linked_to_their_account(): void
    {
        Sanctum::actingAs(
            $this->user1,
            []
        );

        $response = $this->getJson("/api/user/devices-transfers");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(2)
                    ->first(
                        fn (AssertableJson $json) =>
                        $json
                            ->where('id', $this->transfer2->id)
                            ->where('status', $this->transfer2->status->value)

                            ->where('source_user.id', $this->user2->id)
                            ->where('source_user.name', $this->user2->name)
                            ->where('source_user.email', $this->user2->email)
                            ->where('source_user.cpf', $this->user2->cpf)
                            ->where('source_user.phone', $this->user2->phone)

                            ->where('target_user.id', $this->user1->id)
                            ->where('target_user.name', $this->user1->name)
                            ->where('target_user.email', $this->user1->email)
                            ->where('target_user.cpf', $this->user1->cpf)
                            ->where('target_user.phone', $this->user1->phone)

                            ->where('device.id', $this->device2->id)
                            ->where('device.color', $this->device2->color)
                            ->where('device.imei_1', $this->device2->imei_1)
                            ->where('device.imei_2', $this->device2->imei_2)
                            ->where('device.validation_status', $this->device2->validation_status->value)

                            ->where('device.device_model.name', $this->device2->deviceModel->name)
                            ->where('device.device_model.ram', $this->device2->deviceModel->ram)
                            ->where('device.device_model.storage', $this->device2->deviceModel->storage)

                            ->where('device.device_model.brand.name', $this->device2->deviceModel->brand->name)

                            ->has('created_at')
                            ->has('updated_at')
                            ->etc()
                    )
            );
    }

    public function test_a_user_with_no_device_transfer_records_should_receive_an_empty_collection(): void
    {
        $user3 = User::factory()->create();

        Sanctum::actingAs(
            $user3,
            []
        );

        $response = $this->getJson("/api/user/devices-transfers");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEmpty($response->getData());
    }
}
