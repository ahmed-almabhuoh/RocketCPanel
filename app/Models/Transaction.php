<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    const TYPE = [
        'order', 'transfer'
    ];

    const STATUS = [
        'pending', 'approved', 'waiting', 'rejected'
    ];

    const METHODS = [
        'online', 'offer'
    ];

    const CREDIT_TYPE = [
        'withdraw', 'deposit', 'transfer'
    ];

    protected $casts = [
        'method_meta' => 'array',
    ];

    protected $guarded = [];

    public static function booted()
    {

        static::created(function ($transaction) {

            // Create Credit
            registerCredit($transaction);

        });

    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function credits(): HasOne
    {
        return $this->hasOne(Credit::class);
    }

    // Scopes
    public function scopeType($query, $type = 'order')
    {
        return $query->where('type', $type);
    }

    public function scopeStatus($query, $status = 'pending')
    {
        return $query->where('status', $status);
    }

    public function scopeMethod($query, $method = 'online')
    {
        return $query->where('method', $method);
    }

    public function scopeOwn($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->user()->id);
    }

    public function scopeCreditType($query, $creditType = null)
    {
        return $query->where('credit_type', $creditType);
    }
}
