<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\User\GenerateDriverPasswordNotification;
use Carbon\Carbon;
use Filament\Models\Contracts\HasName;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail, HasName
{
    use HasFactory, Notifiable;

    const ROLES = ['Customer', 'Director', 'Driver'];

    const ACCOUNT_STATUS = ['pending', 'active', 'inactive'];

    public static function booted()
    {

        // Only active accounts can access
        // static::addGlobalScope('accountStatus', function ($query, $user) {
        //     if ($user->role == 'Director')
        //         $query->where('account_status', '=', 'active');
        //     else
        //         $query;
        // });

        static::created(function ($user) {

            // Create User's Balance
            Balance::create([
                'user_id' => $user->id,
                'orbits' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Generate a password for drivers account and send verification email
            if ($user->role == 'Driver') {
                $password = Str::random(10);

                $user->password = Hash::make($password);
                $isSaved = $user->save();

                if ($isSaved) {
                    $user->notify(new GenerateDriverPasswordNotification($user, $password));
                } else {
                    info('Failed to generate driver password and send it to his/her email');
                }
            }

            // Create User's Settings
            $settings = new UserSettings();
            $settings->lang = 'ar';
            $settings->time_zone = 'Asia/Jerusalem';
            $settings->login_verification = false;
            $settings->required_personal_information_to_reset_password = true;
            $settings->private_email = true;
            $settings->private_phone = true;
            $settings->private_account = false;
            $settings->user_id = $user->id;
            $settings->save();
            //
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
