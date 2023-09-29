<?php

namespace App\Models;

use App\Enums\Device\DeviceValidationStatus;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\URL;

class Device extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'validation_status' => DeviceValidationStatus::class
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'invoice_id',
        'device_model_id',
        'color',
        'imei_1',
        'imei_2',
        'validation_status'
    ];

    /**
     * Get the user who owns the device.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get device invoices.
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Get the device model.
     */
    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class);
    }

    /**
     * Get device registration transfers.
     */
    public function transfers(): HasMany
    {
        return $this->hasMany(DeviceTransfer::class);
    }

    /**
     * Get the last transfer from the device.
     */
    public function lastTransfer(): DeviceTransfer|null
    {
        return DeviceTransfer::where([
            'device_id' => $this->id
        ])->latest('id')->first();
    }

    /**
     * Generate a url for sharing device data.
     */
    public function generateSharingUrl()
    {
        return URL::temporarySignedRoute('share.device', now()->addHour(), [
            'device' => $this
        ]);
    }
}
