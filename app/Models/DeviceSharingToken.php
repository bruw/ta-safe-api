<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceSharingToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'token',
        'expires_at',
    ];

    /**
     * Get the device that has the token.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
