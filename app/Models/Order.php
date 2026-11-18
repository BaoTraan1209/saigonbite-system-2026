<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Order extends Model {
    protected $fillable = ['hub_id','customer_name','customer_phone','delivery_address','payment_method','subtotal','discount_amount','total_amount','status','notes'];
    protected $casts = ['subtotal'=>'decimal:2','discount_amount'=>'decimal:2','total_amount'=>'decimal:2'];
    public function hub() { return $this->belongsTo(Hub::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function delivery() { return $this->hasOne(Delivery::class); }
}
