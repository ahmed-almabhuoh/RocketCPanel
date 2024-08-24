<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    use HasFactory;

    protected $guarded = [];

    const STATUS = [
        'pending',
        'shipped',
        'delivered',
    ];

    const TYPE = [
        'documents',
        'goods',
    ];

    const WEIGHT_UNIT = ['kg', 'ton'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
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

    public function scopeWeightUnit($query, $weightUnit = 'kg')
    {
        return $query->where('weightUnity', $weightUnit);
    }

    public function scopeType($query, $type = 'goods')
    {
        return $query->where('type', $type);
    }
}
