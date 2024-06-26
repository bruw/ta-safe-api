<?php

namespace App\Http\Resources\Device;

use App\Http\Resources\DeviceModel\DeviceModelResource;
use App\Http\Resources\DeviceTransfer\DeviceTransferBasicResource;
use App\Http\Resources\User\UserPublicResource;
use App\Traits\StringMasks;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DevicePublicResource extends JsonResource
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
            'id' => $this->id,
            'color' => $this->color,
            'imei_1' => self::addAsteriskMaskForImei($this->imei_1),
            'imei_2' => self::addAsteriskMaskForImei($this->imei_2),
            'validation_status' => $this->validation_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserPublicResource($this->user),
            'device_model' => new DeviceModelResource($this->deviceModel),
            'validation_attributes' => $this->whenNotNull($this->validation_attributes),
            'transfers_history' => DeviceTransferBasicResource::collection(
                $this->whenNotNull($this->transfers_history)
            ),
        ];
    }
}
