<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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

    /**
     * Get the user's devices.
     * 
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\User $user
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function getUserDevices(Request $request, User $user): JsonResource
    {
        if ($request->user()->cannot('getDevices', $user)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $currentUser = $request->user();
        $devices = $currentUser->devicesOrderedByIdDesc();

        return DeviceResource::collection($devices);
    }
}
