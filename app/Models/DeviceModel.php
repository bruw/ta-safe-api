<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeviceModel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'chipset',
        'ram',
        'storage',
        'screen_size',
        'screen_resolution',
        'battery_capacity',
        'year_of_manufacture',
        'os'
    ];

    /**
     * Get the device model brand.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get devices that have this model.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class);
    }
}
