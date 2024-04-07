<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserLoginResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedTokenController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): Response
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
    public function destroy(Request $request): Response
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
