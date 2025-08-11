<?php

namespace App\Http\Controllers\Auth;

use App\Dto\Auth\RegisterUserDto;
use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\Auth\UserLoginResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Registers a new user in the system.
     */
    public function register(RegisterUserRequest $request, AuthService $auth): JsonResponse
    {
        $loginDto = $auth->register(RegisterUserDto::fromRequest($request));

        return response()->json(
            FlashMessage::success(trans('actions.auth.success.register'))
                ->merge(['user' => new UserLoginResource($loginDto)]),
            Response::HTTP_CREATED
        );
    }

    /**
     * Authenticate a user.
     */
    public function login(LoginRequest $request, AuthService $auth): JsonResponse
    {
        $loginDto = $auth->login($request->email, $request->password);

        return response()->json(
            FlashMessage::success(trans('actions.auth.success.login'))
                ->merge(['user' => new UserLoginResource($loginDto)]),
            Response::HTTP_OK
        );
    }

    /**
     * Logout the authenticated user by deleting their tokens.
     */
    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return response()->json(
            FlashMessage::success(trans('actions.auth.success.logout')),
            Response::HTTP_OK
        );
    }
}
