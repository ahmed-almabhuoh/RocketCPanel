<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'withdraw', 'deposit', 'transfer'
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


        // static::created(function ($credit) {

        //     // Update Transaction Status
        //     if ($credit->transaction_id) {
        //         Transaction::where('id', $credit->transaction_id)->update([
        //             'status' => 'approved',
        //             'updated_at' => Carbon::now(),
        //         ]);

        //         // Update Balance
        //         $transaction = $credit->transaction()->first();
        //         if ($transaction->status == 'approved') {
        //             // Update User Balance
        //             updateUserBalance($transaction->orbits, $transaction->user_id);
        //         }
        //         return;
        //     }

        //     // Update User Balance
        //     updateUserBalance($credit->credits, $credit->user_id, $credit->type);
        // });
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
