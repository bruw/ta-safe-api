<?php

namespace Tests\Feature\Auth\Logout;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class LogoutAccessTest extends LogoutTestSetUp
{
    public function test_the_user_must_be_logged_out_and_the_access_token_destroyed(): void
    {
        Sanctum::actingAs($this->user);
        $this->user->withAccessToken($this->token->accessToken);

        $this->deleteJson(route('api.auth.logout'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans('actions.auth.success.logout'))
            );

        $this->assertCount(0, $this->user->fresh()->tokens);
    }
}
