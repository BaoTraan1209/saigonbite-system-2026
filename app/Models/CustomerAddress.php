<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerAddress extends Model
{
    protected $fillable = [
        'user_id', 'recipient_name', 'phone', 'street', 'district', 'city',
        'additional_details', 'is_default',
    ];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function formattedAddress(): string
    {
        return implode(', ', array_filter([
            $this->street,
            $this->district,
            $this->city,
            $this->additional_details,
        ]));
    }
}
