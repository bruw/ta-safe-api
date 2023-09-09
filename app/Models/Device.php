<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'is_validated'
    ];

    /**
     * Get the user who owns the device.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get device invoices.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the device model.
     */
    public function deviceModel(): HasOne
    {
        return $this->hasOne(DeviceModel::class);
    }

    /**
     * check whether the device invoice has already been validated.
     */
    public function isValidated(): bool
    {
        return $this->is_validated ? true : false;
    }
}
