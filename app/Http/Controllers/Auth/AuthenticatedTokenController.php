<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;

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
            $user->tokens()->delete();

            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'user' => new UserResource($user),
                'token' => $token
            ]);
        });
    }

    /**
     * Destroy all user authentication tokens.
     */
    public function destroy(Request $request): Response
    {
        return DB::transaction(function () use ($request) {
            $request->user()->currentAccessToken()->delete();

            return response()->noContent();
        });
    }
}
