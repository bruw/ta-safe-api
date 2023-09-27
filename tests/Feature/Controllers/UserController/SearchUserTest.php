<?php

namespace Tests\Feature\Controllers\UserController;

use App\Models\User;
use App\Traits\StringMasks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SearchUserTest extends TestCase
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
        $response = $this->getJson("/api/user/search", [
            'search_term' => $this->targetUser->email
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->where('message', trans('auth.unauthenticated'))
                    ->etc()
            );
    }

    public function test_an_authenticated_user_must_be_authorized_to_search_for_user_by_email(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->getJson("/api/user/search"
            .  '?search_term=' . $this->targetUser->email);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(1)
                    ->first(
                        fn (AssertableJson $json) =>
                        $json->where('id', $this->targetUser->id)
                            ->where('name', $this->targetUser->name)
                            ->where('cpf', self::addAsteriskMaskForCpf($this->targetUser->cpf))
                            ->where('phone', self::addAsteriskMaskForPhone($this->targetUser->phone))
                            ->has('created_at')
                            ->etc()
                    )
            );
    }

    public function test_an_authenticated_user_must_be_authorized_to_search_for_user_by_phone(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->getJson("/api/user/search"
            .  '?search_term=' . $this->targetUser->phone);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(1)
                    ->first(
                        fn (AssertableJson $json) =>
                        $json->where('id', $this->targetUser->id)
                            ->where('name', $this->targetUser->name)
                            ->where('cpf', self::addAsteriskMaskForCpf($this->targetUser->cpf))
                            ->where('phone', self::addAsteriskMaskForPhone($this->targetUser->phone))
                            ->has('created_at')
                            ->etc()
                    )
            );
    }

    public function test_should_return_an_error_if_the_search_term_is_null(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->getJson("/api/user/search"
            .  '?search_term=');

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.search_term.0', trans('validation.required', [
                        'attribute' => trans('validation.attributes.search_term')
                    ]))
                    ->etc()
            );
    }

    public function test_should_return_an_error_if_the_search_term_exceeds_255_characters(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->getJson("/api/user/search"
            .  '?search_term=' . Str::random(256));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has('errors', 1)
                    ->where('errors.search_term.0', trans('validation.max.string', [
                        'max' => 255,
                        'attribute' => trans('validation.attributes.search_term')
                    ]))
                    ->etc()
            );
    }
}
