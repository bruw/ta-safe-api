<?php

namespace Tests\Unit\Actions\Auth\Login;

use App\Dto\Auth\LoginDto;
use App\Exceptions\HttpJsonResponseException;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LoginActionTest extends LoginActionTestSetUp
{
    public function test_should_return_an_instance_of_login_dto_when_registration_is_successful(): void
    {
        $this->assertInstanceOf(LoginDto::class, $this->auth->login($this->user->email, 'password'));
    }

    public function test_should_throw_an_exception_when_the_email_is_incorrect(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('auth.failed'));

        $this->auth->login(fake()->email(), $this->user->password);
    }

    public function test_should_throw_an_exception_when_the_password_is_incorrect(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('auth.failed'));

        $this->auth->login($this->user->email, 'pass');
    }

    public function test_should_throw_an_exception_when_an_internal_server_error_occurs(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.auth.errors.login'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->auth->login($this->user->email, 'password');
    }
}
