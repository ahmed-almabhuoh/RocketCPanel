<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiveCargoToken extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'receive_cargo_tokens';

    const STATUS = ['active', 'inactive'];

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id');
    }

    // Scopes
    public function scopeOwn($query, $userId = null)
    {
        return $query->where('director_id', '=', $userId ?? auth()->id());
    }

    public function scopeStatus($query, $status = 'active')
    {
        return $query->where('status', '=', $status);
    }
}
