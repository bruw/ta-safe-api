<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserLoginResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AuthenticatedTokenController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $request->authenticate();

            $user = $request->user();

            return response()->json(
                FlashMessage::success(trans('actions.auth.login'))
                    ->merge(['user' => new UserLoginResource($user)]),
                Response::HTTP_OK
            );
        });
    }

    /**
     * Destroy all user authentication tokens.
     */
    public function destroy(Request $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $request->user()->currentAccessToken()->delete();

            return response()->json(
                FlashMessage::success(trans('actions.auth.logout')),
                Response::HTTP_OK
            );
        });
    }
}
