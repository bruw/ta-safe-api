<?php

namespace App\Http\Resources\DeviceTransfer;

use App\Http\Resources\Device\DeviceSummaryResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeviceTransferResource extends JsonResource
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
            'status' => $this->status,
            'source_user' => new UserResource($this->sourceUser),
            'target_user' => new UserResource($this->targetUser),
            'device' => new DeviceSummaryResource($this->device),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
