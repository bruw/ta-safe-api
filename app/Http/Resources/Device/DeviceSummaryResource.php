<?php

namespace App\Http\Resources\Device;

use App\Http\Resources\DeviceModel\DeviceModelResource;
use App\Http\Resources\DeviceSharingToken\DeviceSharingTokenResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceSummaryResource extends JsonResource
{
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
            'imei_1' => $this->imei_1,
            'imei_2' => $this->imei_2,
            'validation_status' => $this->validation_status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'sharing_token' => new DeviceSharingTokenResource($this->sharingToken),
            'device_model' => new DeviceModelResource($this->deviceModel),
        ];
    }
}
