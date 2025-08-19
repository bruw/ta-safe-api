<?php

namespace Tests\Feature\Controllers\DeviceSharingController\Create;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class CreateSharingTokenResponseTest extends CreateSharingTokenTestSetUp
{
    public function test_should_response_the_expected_data(): void
    {
        Sanctum::actingAs($this->user);

        $this->postJson($this->route())
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                    ->where('message.text', trans('actions.device.success.token'))
                    ->has('id')
                    ->has('token')
                    ->has('expires_at')
            );
    }
}
