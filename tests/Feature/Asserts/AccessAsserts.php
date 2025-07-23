<?php

namespace Tests\Feature\Asserts;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;

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
                    ->where('message.text', trans('http_exceptions.unauthenticated'))
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
            $response = $this->actingAs($user)
                ->$method($route, $params)
                ->$assertHttpResponse();

            if (! empty($flashMessage)) {
                $response->assertJson(fn (AssertableJson $json) => $json->where('message.type', $flashMessage['type'])
                    ->where('message.text', $flashMessage['msg'])
                );
            }
        }
    }
}
