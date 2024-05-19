<?php

namespace Tests\Feature\Controllers\UserController;

use App\Http\Messages\FlashMessage;
use App\Models\User;
use App\Traits\StringMasks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SearchUserByEmailTest extends TestCase
{
    use RefreshDatabase;
    use StringMasks;

    private User $user;
    private User $targetUser;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->user = User::factory()->create();
        $this->targetUser = User::factory()->create();
    }

    public function test_an_unauthenticated_user_should_not_be_authorized_to_search_for_users(): void
    {
        $response = $this->getJson('api/user/search-by-email'
            . '?email=' . $this->targetUser->email);

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_an_authenticated_user_must_be_authorized_to_search_for_user_by_email(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('api/user/search-by-email'
            . '?email=' . $this->targetUser->email);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) => $json->where('id', $this->targetUser->id)
                    ->where('name', $this->targetUser->name)
                    ->where('cpf', self::addAsteriskMaskForCpf($this->targetUser->cpf))
                    ->where('phone', self::addAsteriskMaskForPhone($this->targetUser->phone))
                    ->has('created_at')
                    ->etc()
            );
    }

    public function test_should_return_an_error_if_the_email_param_is_null(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/user/search-by-email'
            . '?email=');

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.email.0', trans('validation.required', [
                    'attribute' => 'email',
                ]))
        );
    }
}
