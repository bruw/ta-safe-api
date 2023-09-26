<?php

namespace App\Models;

use App\Actions\Device\RegisterDeviceAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Pagination\LengthAwarePaginator;
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
    public static function search(string $searchTerm): LengthAwarePaginator
    {
        $searchTerm = "%{$searchTerm}%";

        return User::where('email', 'LIKE', $searchTerm)
            ->orWhere('phone', 'LIKE', $searchTerm)
            ->paginate(6);
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
