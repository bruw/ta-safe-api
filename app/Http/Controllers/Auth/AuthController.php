<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Auth\RegisterUserDto;
use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\UserLoginResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Registers a new user in the system.
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $loginDto = User::register(RegisterUserDto::fromRequest($request));

        return response()->json(FlashMessage::success(trans_choice('flash_messages.success.registered.m', 1,
            ['model' => trans_choice('model.user', 1)]))
            ->merge(['user' => new UserLoginResource($loginDto)]),
            Response::HTTP_CREATED
        );
    }
}
