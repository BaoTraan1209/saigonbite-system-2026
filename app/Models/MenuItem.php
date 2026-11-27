<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MenuItem extends Model
{
    protected $fillable = [
        'brand_id',
        'name',
        'description',
        'price',
        'is_available',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function hubs(): BelongsToMany
    {
        return $this->belongsToMany(Hub::class, 'hub_menu_items')
            ->withPivot('is_available');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
