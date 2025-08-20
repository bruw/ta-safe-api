<?php

namespace App\Models;

use App\Enums\Device\DeviceValidationStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Lib\Strings\StringHelper;

class Device extends Model
{
    use HasFactory;

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
        'validation_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'validation_status' => DeviceValidationStatus::class,
    ];

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'validation_status' => DeviceValidationStatus::PENDING,
    ];

    /**
     * Interact with the device's color.
     */
    protected function color(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => StringHelper::capitalize(trim($value)),
        );
    }

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
     * Get the attribute validation logs associated with the device.
     */
    public function attributeValidationLogs(): HasMany
    {
        return $this->hasMany(DeviceAttributeValidationLog::class);
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
    public function lastTransfer(): ?DeviceTransfer
    {
        return DeviceTransfer::where([
            'device_id' => $this->id,
        ])->latest('id')->first();
    }

    /**
     * Get the sharing token associated with the device.
     */
    public function sharingToken(): HasOne
    {
        return $this->hasOne(DeviceSharingToken::class);
    }

    /**
     * Invalidates a device record with pending status only.
     */
    public function invalidateRegistration(): bool
    {
        return DB::transaction(function () {
            if ($this->validation_status == DeviceValidationStatus::PENDING) {
                $this->update([
                    'validation_status' => DeviceValidationStatus::REJECTED,
                ]);

                return true;
            }

            return false;
        });
    }
}
