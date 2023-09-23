<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @param \App\Http\Requests\Auth\RegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(RegistrationRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $data = $request->validated();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'cpf' => $data['cpf'],
                'phone' => $data['phone']
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            event(new Registered($user));

            Auth::login($user);

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token
            ]);
        });
    }
}
