<?php

namespace Tests\Feature\Controllers\UserController;

use App\Http\Messages\FlashMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CurrentUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_unauthenticated_user_does_not_have_authorization(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_an_authenticated_user_can_view_their_information(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user,
            []
        );

        $response = $this->getJson('/api/user');
        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) => $json->has(7)
                ->where('id', $user->id)
                ->where('name', $user->name)
                ->where('email', $user->email)
                ->where('cpf', $user->cpf)
                ->where('phone', $user->phone)
                ->has('created_at')
                ->has('updated_at')
                ->missing('password')
                ->etc()
        );
    }
}
