<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\User\SearchUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\Device\DeviceResource;
use App\Http\Resources\DeviceTransfer\DeviceTransferResource;
use App\Http\Resources\User\UserPublicResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Update user profile.
     */
    public function update(UpdateUserRequest $request): Response
    {
        $request->user()
            ->userService()
            ->update($request->toDto());

        return response()->json(FlashMessage::success(
            trans('actions.user.success.update')),
            Response::HTTP_OK
        );
    }

    /**
     * Show current user.
     */
    public function view(Request $request): JsonResource
    {
        return new UserResource($request->user());
    }

    /**
     * Search user by email.
     */
    public function searchByEmail(SearchUserRequest $request): JsonResource
    {
        return new UserPublicResource($request->userByEmail());
    }

    /**
     * Get the user's devices.
     */
    public function devices(Request $request): JsonResource
    {
        return DeviceResource::collection($request->user()->devicesOrderedByUpdate());
    }

    /**
     * Get user devices transfers.
     */
    public function transfers(Request $request): JsonResource
    {
        return DeviceTransferResource::collection($request->user()->userDevicesTransfers());
    }
}
