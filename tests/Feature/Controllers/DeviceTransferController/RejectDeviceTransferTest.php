<?php

namespace Tests\Feature\Controllers\DeviceTransferController;

use App\Enums\Device\DeviceValidationStatus;
use App\Http\Messages\FlashMessage;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceTransfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RejectDeviceTransferTest extends TestCase
{
    use RefreshDatabase;

    private User $sourceUser;
    private User $targetUser;
    private DeviceTransfer $deviceTransfer;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->sourceUser = User::factory()->create();
        $this->targetUser = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $device = Device::factory()
            ->for($this->sourceUser)
            ->for($deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED,
            ]);

        $this->sourceUser->createDeviceTransfer(
            $this->targetUser,
            $device
        );

        $this->deviceTransfer = DeviceTransfer::where([
            'source_user_id' => $this->sourceUser->id,
        ])->firstOrFail();
    }

    public function test_an_unauthenticated_user_must_not_be_allowed_to_reject_a_transfer_proposal(): void
    {
        $response = $this->putJson("api/device-transfers/{$this->deviceTransfer->id}/reject");

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_the_user_who_created_the_transfer_proposal_must_not_be_allowed_to_reject_it(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $response = $this->putJson("api/device-transfers/{$this->deviceTransfer->id}/reject");

        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthorized'))
        );
    }

    public function test_a_user_other_than_the_target_user_should_not_be_allowed_to_reject_the_transfer(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson("api/device-transfers/{$this->deviceTransfer->id}/reject");

        $response->assertForbidden()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthorized'))
        );
    }

    public function test_the_target_user_must_be_authorized_to_reject_the_transfer_proposal(): void
    {
        Sanctum::actingAs($this->targetUser);

        $response = $this->putJson("api/device-transfers/{$this->deviceTransfer->id}/reject");

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.rejected.f', 1, [
                    'model' => trans_choice('model.device_transfer', 1),
                ]))
        );
    }
}
