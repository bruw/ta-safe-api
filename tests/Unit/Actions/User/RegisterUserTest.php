<?php

namespace Tests\Unit\Actions\User;

use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    private User $unregisteredUser;
    private array $data;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->unregisteredUser = User::factory()->make();

        $this->data = [
            'name' => $this->unregisteredUser->name,
            'email' => $this->unregisteredUser->email,
            'password' => "password",
            'cpf' => $this->unregisteredUser->cpf,
            'phone' => $this->unregisteredUser->phone
        ];
    }

    public function test_must_successfully_register_a_user(): void
    {
        User::registerUser($this->data);

        $this->assertDatabaseHas('users', [
            'name' => $this->unregisteredUser->name,
            'email' => $this->unregisteredUser->email,
            'cpf' => $this->unregisteredUser->cpf,
            'phone' => $this->unregisteredUser->phone
        ]);
    }

    public function test_should_return_an_exception_and_not_register_the_user_if_an_internal_error_occurs(): void
    {
        $this->data['cpf'] = Str::random(100);
        $exceptionOcurred = false;

        try {
            User::registerUser($this->data);
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.register_user.unable_to_register_user')
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertNull(
            User::where(['cpf' => $this->unregisteredUser->cpf])->first()
        );
    }
}
