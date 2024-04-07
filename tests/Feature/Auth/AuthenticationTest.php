<?php

namespace Tests\Feature\Auth;

use App\Http\Messages\FlashMessage;
use App\Models\User;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::SetUp();
        $this->user = User::factory()->create();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response->assertOk();

        $response->assertJson(
            fn (AssertableJson $json) =>
            $json->where('user.name', $this->user->name)
                ->where('user.email',  fn (string $email) => str($email)->is($this->user->email))
                ->where('user.id', $this->user->id)
                ->where(
                    'user.created_at',
                    fn ($createdAt) =>
                    Carbon::createFromDate($createdAt)->equalTo($this->user->created_at)
                )
                ->where(
                    'user.updated_at',
                    fn ($updatedAt) =>
                    Carbon::createFromDate($updatedAt)->equalTo($this->user->updated_at)
                )
                ->has('user.token')
                ->missing('user.password')
                ->etc()
        );
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('actions.auth.login_failed'))
                ->etc()
        );
    }
}
