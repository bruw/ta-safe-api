<?php

namespace App\Actions\User;

use App\Exceptions\GeneralJsonException;

use App\Models\User;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserAction
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function execute(): array
    {
        $this->normalizeParamsBeforeAction();

        try {
            return DB::transaction(function () {
                $user = User::create([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'password' => Hash::make($this->data['password']),
                    'cpf' => $this->data['cpf'],
                    'phone' => $this->data['phone']
                ]);

                $token = $user->createToken('api_token')->plainTextToken;

                $newUserData = [
                    'user' => $user,
                    'token' => $token
                ];

                return $newUserData;
            });
        } catch (Exception $e) {
            throw new GeneralJsonException(
                trans('validation.custom.register_user.unable_to_register_user'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function normalizeParamsBeforeAction(): void
    {
        $userName = $this->data['name'];
        $userEmail = $this->data['email'];

        $this->data['name'] = mb_convert_case($userName, MB_CASE_TITLE);
        $this->data['email'] = strtolower(str_replace(' ', '', $userEmail));
    }
}
