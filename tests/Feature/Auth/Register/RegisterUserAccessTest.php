<?php

namespace Tests\Feature\Auth\Register;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;

class RegisterUserAccessTest extends RegisterUserTestSetUp
{
    public function test_should_an_unauthenticated_user_be_able_to_register(): void
    {
        $data = $this->validUserData();

        $this->postJson($this->route(), $data)
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.registered.m', 1, [
                    'model' => trans_choice('model.user', 1),
                ]))
                ->where('user.name', $data['name'])
                ->where('user.email', fn (string $email) => str($email)->is($data['email']))
                ->where('user.cpf', fn (string $cpf) => str($cpf)->is($data['cpf']))
                ->where('user.phone', fn (string $phone) => str($phone)->is($data['phone']))
                ->has('user.token')
                ->missing('user.password')
            );
    }
}
