<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SearchUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Http\Resources\User\UserPublicResource;
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
        $currentUser = $request->user();
        return new UserResource($currentUser);
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
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function userDevices(Request $request): JsonResource
    {
        $currentUser = $request->user();
        $devices = $currentUser->devicesOrderedByIdDesc();

        return DeviceResource::collection($devices);
    }

    /**
     * Search for users by term.
     * 
     * @param \App\Http\Requests\User\SearchUserRequest $request
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function search(SearchUserRequest $request): JsonResource
    {
        $data = $request->validated();
        $users = User::search($data['search_term']);

        return UserPublicResource::collection($users);
    }
}
