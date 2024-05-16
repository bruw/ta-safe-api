<?php

namespace App\Models;

use App\Actions\Device\CreateSharingTokenAction;
use App\Actions\Device\ValidateDeviceRegistrationAction;
use App\Enums\Device\DeviceValidationStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Lib\Strings\StringHelper;

class Device extends Model
{
    use HasFactory, SoftDeletes;

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
     * Invoke the create device sharing token action.
     */
    public function createSharingToken(): DeviceSharingToken
    {
        $createToken = new CreateSharingTokenAction($this);

        return $createToken->execute();
    }

    /**
     * Returns a key-value array for the validated attributes.
     */
    public function validatedAttributes(): array
    {
        return $this->attributeValidationLogs->pluck(
            'validated',
            'attribute_label'
        )->toArray();
    }

    /**
     * Get device registration transfers history.
     */
    public function transfersHistory(): HasMany
    {
        return $this->hasMany(DeviceTransfer::class)->acceptedAndOrdered();
    }

    /**
     * Invoke device registration validation.
     */
    public function validateRegistration(string $cpf, string $name, string $products): bool
    {
        $action = new ValidateDeviceRegistrationAction(
            $this,
            $cpf,
            $name,
            $products
        );

        return $action->execute();
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
