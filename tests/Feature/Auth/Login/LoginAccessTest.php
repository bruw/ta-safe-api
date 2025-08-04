<?php

namespace Tests\Feature\Auth\Login;

use App\Http\Messages\FlashMessage;
use Illuminate\Testing\Fluent\AssertableJson;

class LoginAccessTest extends LoginTestSetUp
{
    public function test_a_user_should_be_able_to_login(): void
    {
        $this->postJson($this->route(), [
            'email' => $this->user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans('actions.auth.success.login'))
                ->where('user.id', $this->user->id)
                ->where('user.name', $this->user->name)
                ->where('user.email', fn (string $email) => str($email)->is($this->user->email))
                ->where('user.cpf', fn (string $cpf) => str($cpf)->is($this->user->cpf))
                ->where('user.phone', fn (string $phone) => str($phone)->is($this->user->phone))
                ->has('user.token')
                ->missing('user.password')
            );
    }
}
