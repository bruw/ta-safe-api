<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'cpf' => $this->user->cpf,
            'phone' => $this->user->phone,
            'created_at' => $this->user->created_at,
            'updated_at' => $this->user->updated_at,
            'token' => $this->token,
        ];
    }
}
