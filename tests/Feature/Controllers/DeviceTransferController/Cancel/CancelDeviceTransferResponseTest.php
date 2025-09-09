<?php

namespace Tests\Feature\Controllers\DeviceTransferController\Cancel;

use App\Enums\Device\DeviceTransferStatus;
use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class CancelDeviceTransferResponseTest extends CancelDeviceTransferTestSetUp
{
    public function test_should_return_an_expected_base_data_response(): void
    {
        Sanctum::actingAs($this->sourceUser);

        $this->postJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                    ->where('message.text', trans('actions.device_transfer.success.cancel'))
                    ->where('transfer.id', $this->transfer->id)
                    ->where('transfer.status', DeviceTransferStatus::CANCELED->value)
                    ->has('transfer.source_user')
                    ->has('transfer.target_user')
                    ->has('transfer.device')
                    ->has('transfer.created_at')
                    ->has('transfer.updated_at')
                    ->etc()
            );
    }
}
