<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @param \App\Http\Requests\Auth\RegisterUserReques $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function store(RegisterUserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $newUser = User::registerUser($data);

        return response()->json([
            'user' => new UserResource($newUser['user']),
            'token' => $newUser['token']
        ]);
    }
}
