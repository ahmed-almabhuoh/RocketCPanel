<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;

class Credit extends Model
{
    use HasFactory;

    protected $fillable = [
        'credits',
        'balance_before',
        'balance_after',
        'type',
        'user_id',
        'balance_id',
        'transaction_id',
        'reason',
        'created_at',
        'updated_at',
    ];

    const TYPE = [
        'withdraw',
        'deposit',
        'transfer'
    ];

    public static function booted()
    {

        // static::creating(function ($credit) {


        //     // Update Transaction Status
        //     if ($credit->transaction_id) {
        //         Transaction::where('id', $credit->transaction_id)->update([
        //             'status' => 'waiting',
        //             'updated_at' => Carbon::now(),
        //         ]);
        //     }
        // });


        static::created(function ($credit) {

            // Update User Balance
            $response = Http::withHeaders([
                'public' => env('CONTROL_PANEL_PUBLIC_KEY'),
                'secret' => env('CONTROL_PANEL_PRIVATE_KEY'),
                'Accept' => 'application/json',
            ])->get(env('ROCKET_SHM__DASHBOARD_LINK_PRODUCTION') . 'api/build/user/config/update-balance/' . Crypt::encrypt($credit->id));

            info($response);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    // Scope
    public function scopeType($query, $type = 'withdraw')
    {
        return $query->where('type', '=', $type);
    }

    public function scopeOwn($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->user()->id);
    }
}
