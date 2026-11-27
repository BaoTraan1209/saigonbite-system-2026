<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'shipper_name',
        'shipper_phone',
        'status',
        'picked_up_at',
        'delivered_at',
        'cod_amount',
        'cash_collected',
        'failure_reason',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cod_amount' => 'decimal:2',
        'cash_collected' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
