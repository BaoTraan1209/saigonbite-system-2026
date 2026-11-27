<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'hub_id',
        'brand_id',
        'brand_name',
        'customer_id',
        'customer_address_id',
        'voucher_code',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'payment_method',
        'payment_status',
        'subtotal',
        'delivery_fee',
        'surcharge',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
        'idempotency_key',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'surcharge' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function hub(): BelongsTo
    {
        return $this->belongsTo(Hub::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }
}
