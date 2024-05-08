<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceAttributeValidationLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'attribute_source',
        'attribute_label',
        'attribute_value',
        'invoice_attribute_label',
        'invoice_attribute_value',
        'invoice_validated_value',
        'similarity_ratio',
        'min_similarity_ratio',
        'validated',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'validated' => 'boolean',
    ];

    /**
     * Get the user associated with the validation log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the device associated with the validation log.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
