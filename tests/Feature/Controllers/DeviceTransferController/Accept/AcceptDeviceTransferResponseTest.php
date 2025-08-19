<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Accept;

use App\Enums\Device\DeviceTransferStatus;
use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class AcceptDeviceTransferResponseTest extends AcceptDeviceTransferTestSetUp
{
    public function test_should_return_an_expected_base_data_response(): void
    {
        Sanctum::actingAs($this->targetUser);

        $this->postJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                    ->where('message.text', trans('actions.device_transfer.success.accept'))
                    ->where('transfer.id', $this->transfer->id)
                    ->where('transfer.status', DeviceTransferStatus::ACCEPTED->value)
                    ->has('transfer.source_user')
                    ->has('transfer.target_user')
                    ->has('transfer.device')
                    ->has('transfer.created_at')
                    ->has('transfer.updated_at')
                    ->etc()
            );
    }
}
