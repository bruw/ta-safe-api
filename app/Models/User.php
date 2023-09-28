<?php

namespace App\Models;

use App\Actions\Device\RegisterDeviceAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'phone'
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
     * Get the user devices.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    /**
     * Get user transfers devices.
     */
    public function devicesTransfers()
    {
        return DeviceTransfer::where([
            'source_user_id' => $this->id
        ])->orWhere([
            'target_user_id' => $this->id
        ])->orderByDesc('id')->get();
    }

    /**
     * Get the user's devices sorted by Id Desc.
     */
    public function devicesOrderedByIdDesc()
    {
        return Device::where([
            'user_id' => $this->id
        ])->orderByDesc('id')->get();
    }

    /**
     * Search for users by email or phone.
     */
    public static function search(string $searchTerm): Collection
    {
        $searchTerm = strtolower($searchTerm);

        return User::where('email', $searchTerm)
            ->orWhere('phone', $searchTerm)
            ->get();
    }

    /**
     * Invoke device registration action.
     */
    public function registerDevice(array $data): bool
    {
        $registerDevice = new RegisterDeviceAction(
            $this,
            $data
        );

        return $registerDevice->execute();
    }
}
