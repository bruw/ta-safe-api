<?php

namespace Tests\Feature\Controllers\UserController\View;

use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class ViewUserResponseTest extends ViewUserTestSetUp
{
    public function test_should_return_the_expected_response_data_and_structure(): void
    {
        Sanctum::actingAs($this->user);

        $this->getJson($this->route())
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('id', $this->user->id)
                    ->where('name', $this->user->name)
                    ->where('email', $this->user->email)
                    ->where('cpf', $this->user->cpf)
                    ->where('phone', $this->user->phone)
                    ->has('created_at')
                    ->has('updated_at')
                    ->missing('password')
            );
    }
}
