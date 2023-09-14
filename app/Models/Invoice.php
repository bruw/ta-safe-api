<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'access_key',
        'consumer_cpf',
        'consumer_name',
        'product_description'
    ];

    /**
     * Get the device associated with the invoice.
     */
    public function device(): HasOne
    {
        return $this->hasOne(Device::class);
    }
}
