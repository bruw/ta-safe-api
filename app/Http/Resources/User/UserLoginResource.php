<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;

class UserLoginResource extends UserResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $parent = parent::toArray($request);

        return array_merge($parent, [
            'token' => $this->createToken('auth-token')->plainTextToken
        ]);
    }
}
