<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Jobs\Auth\User\UserCreatedJob;
use App\Notifications\User\GenerateDriverPasswordNotification;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail, HasName, FilamentUser
{
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }

    const ROLES = ['Customer', 'Director', 'Driver'];

    const ACCOUNT_STATUS = ['pending', 'active', 'inactive'];

    public static function booted()
    {
        static::created(function ($user) {
            // UserCreatedJob::dispatch($user)->onQueue('auth');
            $response = Http::withHeaders([
                'public' => env('CONTROL_PANEL_PUBLIC_KEY'),
                'secret' => env('CONTROL_PANEL_PRIVATE_KEY'),
                'Accept' => 'application/json',
            ])->get(env('ROCKET_SHM__DASHBOARD_LINK_PRODUCTION') . 'api/build/user/password/' . Crypt::encrypt($user->id));

            $response = Http::withHeaders([
                'public' => env('CONTROL_PANEL_PUBLIC_KEY'),
                'secret' => env('CONTROL_PANEL_PRIVATE_KEY'),
                'Accept' => 'application/json',
            ])->get(env('ROCKET_SHM__DASHBOARD_LINK_PRODUCTION') . 'api/build/user/balance/' . Crypt::encrypt($user->id));

            $response = Http::withHeaders([
                'public' => env('CONTROL_PANEL_PUBLIC_KEY'),
                'secret' => env('CONTROL_PANEL_PRIVATE_KEY'),
                'Accept' => 'application/json',
            ])->get(env('ROCKET_SHM__DASHBOARD_LINK_PRODUCTION') . 'api/build/user/settings/' . Crypt::encrypt($user->id));
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];

    protected $guarded = [];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->where('role', 'Director');
    }

    public function address(): HasOne
    {
        return $this->hasOne(UserAddress::class);
    }

    public function settings(): HasOne
    {
        return $this->hasOne(UserSettings::class);
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'driver_id', 'id');
    }

    public function vehicleScheduleDirector(): HasMany
    {
        return $this->hasMany(VehicleSchedule::class, 'director_id', 'id');
    }

    public function vehicleScheduleDriver(): HasMany
    {
        return $this->hasMany(VehicleSchedule::class, 'driver_id', 'id');
    }

    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'director_id', 'id');
    }

    public function balance(): HasOne
    {
        return $this->hasOne(Balance::class);
    }

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'director_id', 'id');
    }

    public function transferCodes(): HasMany
    {
        return $this->hasMany(TransferCode::class);
    }

    public function transferLogs(): HasMany
    {
        return $this->hasMany(TransferLog::class, 'user_id', 'id');
    }

    public function cargos(): HasMany
    {
        return $this->hasMany(Cargo::class, 'customer_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'customer_id');
    }

    // User Blocked from Another Users
    public function blocks(): HasMany
    {
        return $this->hasMany(UserBlockList::class, 'blocked_id', 'id');
    }

    // List of Users Whom Customer Blocked
    public function blocked(): HasMany
    {
        return $this->hasMany(UserBlockList::class, 'blocker_id', 'id');
    }

    public function blockedUsers(): HasMany
    {
        return $this->hasMany(User::class, 'blocked_id', 'id');
    }

    public function token(): HasMany
    {
        return $this->hasMany(ReceiveCargoToken::class, 'director_id', 'id');
    }

    public function cargoCredentials(): HasMany
    {
        return $this->hasMany(ReceivingCargoCredential::class, 'customer_id');
    }

    public function driverPosition(): HasOne
    {
        return $this->hasOne(DriverPosition::class, 'driver_id', 'id');
    }

    public function securityQuestion(): HasOne
    {
        return $this->hasOne(SecurityQuestion::class, 'user_id', 'id');
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class, 'driver_id', 'id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'sender_id', 'id')->orWhere('receiver_id', $this->id);
    }

    // Scopes
    public function scopePerson($query, String $role = 'Customer')
    {
        return $query->where('role', $role);
    }

    public function scopeOwn($query)
    {
        return $query->where('user_id', '=', auth()->user()->id);
    }

    public function scopeStatus($query, $status = 'active')
    {
        return $query->where('account_status', '=', $status);
    }

    /**
     * The channels the user receives notification broadcasts on.
     */
    // public function receivesBroadcastNotificationsOn(): string
    // {
    //     return 'users.'.$this->id;
    // }

    public function getFilamentName(): string
    {
        return $this->fullName;
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->fname}";
    }
}
