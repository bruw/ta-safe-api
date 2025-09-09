<?php

namespace Tests\Feature\Controllers\UserController\Search;

use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;

class SearchUserByEmailResponseTest extends SearchUserByEmailTestSetUp
{
    public function test_should_return_the_expected_response_data_and_structure(): void
    {
        Sanctum::actingAs($this->user);

        $this->getJson($this->route(email: $this->targetUser->email))
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->where('id', $this->targetUser->id)
                    ->where('name', $this->targetUser->name)
                    ->where('cpf', $this->addAsteriskMaskForCpf($this->targetUser->cpf))
                    ->where('phone', $this->addAsteriskMaskForPhone($this->targetUser->phone))
                    ->has('created_at')
                    ->missing('password')
            );
    }
}
