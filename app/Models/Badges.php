<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badges extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'status',
    ];

    const STATUS = [
        'active',
        'inactive'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'badge_user', 'badge_id', 'user_id');
    }

    // Scopes
    public function scopeStatus($query, $status = 'active')
    {
        return $query->where('status', $status);
    }
}
