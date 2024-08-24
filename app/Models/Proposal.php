<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'customer_id',
        'trip_id',
        'cargo_id',
        'created_at',
        'updated_at'
    ];

    const STATUS = [
        'pending',
        'seen',
        'accepted',
        'rejected'
    ];

    static public function booted()
    {

        static::updated(function ($proposal) {
            if ($proposal->status == 'accepted' && ! ReceivingCargoCredential::where('proposal_id', $proposal->id)->count()) {
                $public = Str::uuid();
                $secret = Hash::make(Str::random(20));

                ReceivingCargoCredential::create([
                    'public' => $public,
                    'secret' => $secret,
                    'proposal_id' => $proposal->id,
                    'customer_id' => $proposal->customer_id,
                ]);

                // Update the proposal's cargo
                Cargo::whereHas('proposals', function ($query) use ($proposal) {
                    $query->where('id', $proposal->id);
                })->where('customer_id', $proposal->customer_id)->update([
                    'status' => 'shipped',
                ]);

                // Fire a job to send email with cargo credentials
            }
        });

        // Update Trip Information
        static::created(function ($proposal) {
            $trip = $proposal->trip()->status('open')->first();
            $userBalance = Auth::user()->balance;

            // Update User Balance
            $userBalance->orbits -= $trip->trip_proposal_credits;
            $userBalance->updated_at = Carbon::now();
            $userBalance->save();

            // Add Proposal Credits
            Credit::create([
                'credits' => $trip->trip_proposal_credits,
                'balance_before' => $userBalance->orbits,
                'balance_after' => $userBalance->orbits - $trip->trip_credits,
                'type' => 'withdraw',
                'user_id' => $trip->director_id,
                'balance_id' => $userBalance->id,
                'transaction_id' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            // Update Trip Information
            if ($trip->proposals()->count() >= $trip->trip_proposal_limitation) {
                $trip->status = 'close';
                $trip->updated_at = Carbon::now();
                $trip->save();
            }
        });
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function cargoCredentials(): HasOne
    {
        return $this->hasOne(ReceivingCargoCredential::class, 'proposal_id', 'id');
    }

    // Scopes
    public function scopeOwn($query, $userId = null)
    {
        return $query->where('customer_id', $userId ?? auth()->id());
    }

    public function scopeStatus($query, $status = 'pending')
    {
        return $query->where('status', $status);
    }
}
