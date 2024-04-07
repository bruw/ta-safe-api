<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Messages\FlashMessage;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Resources\User\UserLoginResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterUserRequest $request): Response
    {
        $data = $request->validated();

        $newUser = User::registerUser($data);

        return response()->json(
            FlashMessage::success(trans_choice('flash_messages.success.registered.m', 1, [
                'model' => trans_choice('model.user', 1),
            ]))->merge(['user' => new UserLoginResource($newUser)]),
            Response::HTTP_CREATED
        );
    }
}
