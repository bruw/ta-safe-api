<?php

namespace App\Http\Resources\DeviceModel;

use App\Http\Resources\Brand\BrandResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'ram' => $this->ram,
            'storage' => $this->storage,
            'brand' => new BrandResource($this->brand),
        ];
    }
}
