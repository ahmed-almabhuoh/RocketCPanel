<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'orbits', 'type', 'user_id', 'transfer_code_id', 'created_at'
    ];

    const TYPE = [
        'send', 'received'
    ];

    const UPDATED_AT = null;

    public function transferCode(): BelongsTo
    {
        return $this->belongsTo(TransferCode::class, 'transfer_code_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Scopes
    public function scopeType($query, $type = 'send')
    {
        return $query->where('type', $type);
    }
}
