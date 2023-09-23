<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Show current user.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\UserResource
     */
    public function currentUser(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update user.
     * 
     * @param \App\Http\Requests\User\UpdateUserRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request): Response
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();
            $data = $request->validated();

            $user->update($data);

            return response()->noContent();
        });
    }
}
