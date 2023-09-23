<?php

namespace App\Http\Resources\User;

use App\Traits\StringMasks;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPublicResource extends JsonResource
{
    use StringMasks;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'cpf' => self::addAsteriskMaskForCpf($this->cpf),
            'phone' => self::addAsteriskMaskForPhone($this->phone),
            'created_at' => $this->created_at
        ];
    }
}
