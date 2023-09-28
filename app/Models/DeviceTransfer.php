<?php

namespace App\Models;

use App\Enums\Device\DeviceTransferStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceTransfer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'source_user_id',
        'target_user_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => DeviceTransferStatus::class
    ];

    /**
     * Get the device transfer.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Get the user who created the transfer.
     */
    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_user_id');
    }

    /**
     * Get the target user of the transfer
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}