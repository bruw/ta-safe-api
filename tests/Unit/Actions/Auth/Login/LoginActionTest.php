<?php

namespace Tests\Unit\Actions\Auth\Login;

use App\Dto\Auth\LoginDto;
use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LoginActionTest extends LoginActionTestSetUp
{
    public function test_should_return_an_instance_of_login_dto_when_registration_is_successful(): void
    {
        $this->assertInstanceOf(LoginDto::class, User::login($this->user, 'password'));
    }

    public function test_should_throw_an_exception_when_the_password_is_incorrect(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNAUTHORIZED);
        $this->expectExceptionMessage(trans('auth.password'));

        User::login($this->user, 'pass');
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

        User::login($this->user, 'password');
    }
}
