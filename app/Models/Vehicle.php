<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    const STATUS = ['active', 'inactive'];
    const SIZE = ['big', 'middle', 'small'];

    public function director(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(VehicleSchedule::class);
    }

    public function vehicleCapacity(): HasOne
    {
        return $this->hasOne(VehicleCapacity::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    // Scopes
    public function scopeStatus($query, $status = 'active')
    {
        return $query->where('status', '=', $status);
    }

    public function scopeSize($query, $size = 'big')
    {
        return $query->where('size', '=', $size);
    }

    public function scopeOwn($query, int $id = 0)
    {
        return $query->where('director_id', $id == 0 ? auth()->user()->id : $id);
    }

    // Attributes
    public function getInsuranceIconAttribute(): String
    {
        return !is_null($this->insurance) ? 'icon-2x text-success flaticon2-check-mark' : 'icon-2x text-danger flaticon-cancel';
    }

    public function getStatusIconAttribute(): String
    {
        return $this->status == 'active' ? 'icon-2x text-success flaticon2-check-mark' : 'icon-2x text-danger flaticon-cancel';
    }
}
