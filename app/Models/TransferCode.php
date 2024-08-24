<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransferCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'secret', 'code', 'only_for', 'status', 'user_id', 'limited', 'time_to_use', 'created_at'
    ];

    const STATUS = [
        'active', 'inactive'
    ];

    const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TransferLog::class, 'transfer_code_id', 'id');
    }


    // Scopes
    public function scopeOwn($query, $userId = null)
    {
        return $query->where('user_id', $userId ?? auth()->user()->id);
    }

    public function scopeStatus($query, $status = 'active')
    {
        return $query->where('status', $status);
    }
}
