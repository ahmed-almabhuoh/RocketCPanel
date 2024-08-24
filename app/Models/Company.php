<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'director_id', 'id');
    }

    public function scopeOwn($query, $userId = null)
    {
        return $query->where('director_id', $userId ?? auth()->user()->id);
    }
}
