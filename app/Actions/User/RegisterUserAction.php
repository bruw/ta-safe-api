<?php

namespace App\Actions\User;

use App\Exceptions\HttpJsonResponseException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserAction
{
    public function __construct(private array $data) {}

    public function execute(): User
    {
        try {
            return DB::transaction(function () {
                return User::create([
                    'name' => $this->data['name'],
                    'email' => $this->data['email'],
                    'password' => Hash::make($this->data['password']),
                    'cpf' => $this->data['cpf'],
                    'phone' => $this->data['phone'],
                ]);
            });
        } catch (Exception $e) {
            throw new HttpJsonResponseException(
                trans('validation.custom.register_user.unable_to_register_user'),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
