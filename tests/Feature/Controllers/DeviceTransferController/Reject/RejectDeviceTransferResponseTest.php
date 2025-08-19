<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Reject;

use App\Enums\Device\DeviceTransferStatus;
use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class RejectDeviceTransferResponseTest extends RejectDeviceTransferTestSetUp
{
    public function test_should_return_an_expected_base_data_response(): void
    {
        Sanctum::actingAs($this->targetUser);

        $this->postJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                    ->where('message.text', trans('actions.device_transfer.success.reject'))
                    ->where('transfer.id', $this->transfer->id)
                    ->where('transfer.status', DeviceTransferStatus::REJECTED->value)
                    ->has('transfer.source_user')
                    ->has('transfer.target_user')
                    ->has('transfer.device')
                    ->has('transfer.created_at')
                    ->has('transfer.updated_at')
                    ->etc()
            );
    }
}
