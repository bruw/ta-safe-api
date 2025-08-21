<?php

namespace Tests\Feature\Controllers\UserController\Transfers;

use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class UserDevicesTransfersResponseTest extends UserDevicesTransfersTestSetUp
{
    public function test_a_user_with_device_transfers_should_receive_a_collection_of_device_transfers(): void
    {
        Sanctum::actingAs($this->user);

        $this->getJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(1)
                    ->first(fn (AssertableJson $json) => $json->where('id', $this->transfer->id)
                        ->where('status', $this->transfer->status->value)
                        ->has('source_user')
                        ->has('target_user')
                        ->has('device')
                        ->has('created_at')
                        ->has('updated_at')
                    )
            );
    }
}
