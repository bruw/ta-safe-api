<?php

namespace App\Http\Resources\Device;

use App\Http\Resources\DeviceModel\DeviceModelResource;
use App\Http\Resources\DeviceSharingToken\DeviceSharingTokenResource;
use App\Http\Resources\DeviceTransfer\DeviceTransferBasicResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceResource extends JsonResource
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
            'access_key' => $this->invoice->access_key,
            'validation_status' => $this->validation_status,
            'sharing_token' => new DeviceSharingTokenResource($this->sharingToken),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->user),
            'device_model' => new DeviceModelResource($this->deviceModel),
            'validation_attributes' => $this->validation_attributes,
            'transfers_history' => DeviceTransferBasicResource::collection($this->transfersHistory),
        ];
    }
}
