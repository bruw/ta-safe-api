<?php

namespace App\Models;

use App\Actions\Device\RejectDeviceTransferAction;
use App\Services\Device\DeviceService;
use App\Services\DeviceTransfer\DeviceTransferService;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Lib\Strings\StringHelper;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Interact with the user's name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => StringHelper::capitalize(trim($value)),
        );
    }

    /**
     * Interact with the user's email.
     */
    protected function email(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => trim($value),
        );
    }

    /**
     * Get the user devices.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Get the device attribute validation logs associated with the user.
     */
    public function deviceAttributeValidationLogs(): HasMany
    {
        return $this->hasMany(DeviceAttributeValidationLog::class);
    }

    /**
     * Get user transfers devices.
     */
    public function userDevicesTransfers(): Collection
    {
        return DeviceTransfer::where(function ($query) {
            $query->where('source_user_id', $this->id)
                ->orWhere('target_user_id', $this->id);
        })->orderByDesc('updated_at')->get();
    }

    /**
     * Get the user's devices sorted by updated_at desc.
     */
    public function devicesOrderedByUpdate()
    {
        return Device::where([
            'user_id' => $this->id,
        ])->orderByDesc('updated_at')->get();
    }

    /*
    ================= ** Actions ** ==========================================================================
    */

    /**
     * Returns an instance of the DeviceService, which provides methods
     * for performing operations with the user's devices.
     */
    public function deviceService(): DeviceService
    {
        return new DeviceService($this);
    }

    /**
     * Returns an instance of the DeviceTransferService, which provides methods
     * for performing operations with device transfers for the user.
     */
    public function deviceTransferService(): DeviceTransferService
    {
        return new DeviceTransferService($this);
    }

    /**
     * Invoke the action to reject the device transfer.
     */
    public function rejectDeviceTransfer(DeviceTransfer $deviceTransfer): bool
    {
        $rejectTransfer = new RejectDeviceTransferAction(
            $deviceTransfer
        );

        return $rejectTransfer->execute();
    }
}
