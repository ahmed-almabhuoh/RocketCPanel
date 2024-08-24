<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    use HasFactory;

    protected $guarded = [];

    const RELATIONSHIPS = [
        'Father',
        'Mother',
        'Spouse',
        'Sibling',
        'Friend',
        'Colleague',
        'Relative',
        'Child',
        'Guardian',
        'Other',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id', 'id')->where('role', 'Driver');
    }

    // Scopes
    public function scopeRelationship($query, $relationship = 'all')
    {
        if ($relationship != 'all')
            return $query->where('relationship', '=', $relationship);

        return $query;
    }
}
