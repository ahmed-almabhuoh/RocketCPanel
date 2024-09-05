<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_ar',
        'title_en',
        'credits',
        'fixed',
        'status',
        'percentage',
        'created_at',
        'updated_at'
    ];

    const STATUS = ['active', 'inactive'];


    // Scopes
    public function statusScope($query, $status = 'active')
    {
        return $query->where('status', $status);
    }
}
