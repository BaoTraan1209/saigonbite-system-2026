<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hub extends Model
{
    protected $fillable = ['name', 'address', 'phone', 'capacity', 'status'];

    protected $casts = ['capacity' => 'integer'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'hub_brands');
    }

    public function getCurrentLoadAttribute($value): int
    {
        if ($value !== null) {
            return (int) $value;
        }

        return $this->orders()->whereIn(
            'status',
            ['submitted', 'accepted', 'preparing', 'ready', 'delivering']
        )->count();
    }
}
