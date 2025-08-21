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
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Show current user.
     */
    public function view(Request $request): JsonResource
    {
        return new UserResource($request->user());
    }

    /**
     * Update user profile.
     */
    public function update(UpdateUserRequest $request): Response
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();
            $data = $request->validated();

            $user->update($data);

            return response()->json(
                FlashMessage::success(trans_choice('flash_messages.success.updated.m', 1, [
                    'model' => trans_choice('model.profile', 1),
                ])),
                Response::HTTP_OK
            );
        });
    }

    /**
     * Get the user's devices.
     */
    public function userDevices(Request $request): JsonResource
    {
        $currentUser = $request->user();
        $devices = $currentUser->devicesOrderedByUpdate();

        return DeviceResource::collection($devices);
    }

    /**
     * Get user devices transfers.
     */
    public function userDevicesTransfers(Request $request): JsonResource
    {
        $currentUser = $request->user();
        $transfers = $currentUser->userDevicesTransfers();

        return DeviceTransferResource::collection($transfers);
    }

    /**
     * Search user by email.
     */
    public function searchByEmail(SearchUserRequest $request): JsonResource
    {
        return new UserPublicResource(
            $request->userByEmail()
        );
    }
}
