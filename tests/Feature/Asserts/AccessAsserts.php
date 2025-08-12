<?php

namespace Tests\Feature\Asserts;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

trait AccessAsserts
{
    /**
     * Asserts that the given $users do not have access to the given $route.
     */
    public function assertAccessUnauthorizedTo(string $route, string $httpVerb, array $params = []): void
    {
        $method = strtolower($httpVerb) . 'Json';

        $this->$method($route, $params)
            ->assertUnauthorized()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('auth.unauthenticated'))
            );
    }

    /**
     * Asserts that the given $users have access to the given $route.
     */
    public function assertAccessTo(
        string $route,
        string $httpVerb,
        string $assertHttpResponse,
        array $users,
        array $flashMessage = [],
        array $params = [],
    ) {
        $method = strtolower($httpVerb) . 'Json';

        foreach ($users as $user) {
            Sanctum::actingAs($user);

            $response = $this->$method($route, $params)
                ->$assertHttpResponse();

            if (! empty($flashMessage)) {
                $response->assertJson(fn (AssertableJson $json) => $json->where('message.type', $flashMessage['type'])
                    ->where('message.text', $flashMessage['msg'])
                );
            }
        }
    }

    /**
     * Asserts that the given $users do not have access to the given $route.
     */
    public function assertNoAccessTo(
        string $route,
        string $httpVerb,
        array $users,
        array $params = [],
    ) {
        $method = strtolower($httpVerb) . 'Json';

        foreach ($users as $user) {
            Sanctum::actingAs($user);

            $this->$method($route, $params)
                ->assertForbidden()
                ->assertJson(
                    fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                        ->where('message.text', trans('auth.unauthorized'))
                );
        }
    }
}
