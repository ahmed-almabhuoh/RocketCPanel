<?php

namespace App\Models;

use App\Jobs\ConfigBlockedTripsJob;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        "from_lat",
        "from_lng",
        "to_lat",
        "to_lng",
        "from_city",
        "to_city",
        "start_at",
        "end_at",
        "description",
        "trip_credits",
        "trip_proposal_credits",
        'trip_proposal_limitation',
        "when_exceed",
        "vehicle_id",
        "director_id",
        "created_at",
        "updated_at",
    ];

    const WHEN_EXCEED = [
        'gvwr',
        'weight'
    ];

    const STATUS = [
        'open',
        'close'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public static function booted()
    {
        // Hide Blocked Trips
        // static::addGlobalScope('trips.blocking', function (Builder $query) {
        //     $query->whereDoesntHave('blocks', function (Builder $query) {
        //         $query->where('user_id', auth()->id());
        //     });
        // });

        // Hide Blocked Trips Depending On User
        // static::addGlobalScope('trips.block.director', function (Builder $query) {
        //     $query->whereHas('director', function (Builder $query) {
        //         $query->whereDoesntHave('blocks', function (Builder $query) {
        //             $query->where('blocker_id', auth()->id());
        //         });
        //     });
        // });

        static::created(function ($trip) {

            // Generate Trip Code, and notify customers who submitted proposals with status code = accepted
            $trip->code = Str::uuid();
            $trip->save();

            $user = User::where('id', $trip->director_id)->first();
            $userBalance = $user->balance;

            // Update User Balance

            // Create Trip Configs If It Is Not Created
            if (is_null($trip->configuration()->first())) {
                TripConfiguration::create([
                    'trip_id' => $trip->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // Create Credits
            Credit::create([
                'credits' => $trip->trip_credits,
                'balance_before' => $userBalance->orbits,
                'balance_after' => $userBalance->orbits - $trip->trip_credits,
                'type' => 'withdraw',
                'user_id' => $trip->director_id,
                'balance_id' => $userBalance->id,
                'transaction_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Config Blocked Trips - This should convert to queues when using redis
            // ConfigBlockedTripsJob::dispatch($trip->director)->onQueue('tripConfigs');
        });
    }

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id', 'id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function configuration(): HasOne
    {
        return $this->hasOne(TripConfiguration::class, 'trip_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(BlockedTrips::class, 'trip_id', 'id');
    }

    public function driverPosition(): HasOne
    {
        return $this->hasOne(DriverPosition::class, 'trip_id');
    }

    // Scopes
    public function scopeWhenExceed($query, $whenExceed = 'gvwr')
    {
        return $query->where('when_exceed', $whenExceed);
    }

    public function scopeOwn($query, $userId = null)
    {
        return $query->where('director_id', $userId ?? auth()->user()->id);
    }

    public function scopeStatus($query, $status = 'open')
    {
        return $query->where('status', $status);
    }
}
