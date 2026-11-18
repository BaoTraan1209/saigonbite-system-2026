<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('orders',function(Blueprint $t){$t->id();$t->foreignId('hub_id')->constrained();$t->string('customer_name');$t->string('customer_phone',30);$t->text('delivery_address');$t->enum('payment_method',['cash','card','online']);$t->decimal('subtotal',12,2);$t->decimal('discount_amount',12,2)->default(0);$t->decimal('total_amount',12,2);$t->enum('status',['submitted','accepted','rejected','preparing','ready','delivering','completed','delivery_failed'])->default('submitted');$t->text('notes')->nullable();$t->timestamps();});
  Schema::create('order_items',function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->cascadeOnDelete();$t->foreignId('menu_item_id')->nullable()->constrained()->nullOnDelete();$t->string('item_name');$t->unsignedInteger('quantity');$t->decimal('unit_price',12,2);$t->decimal('line_total',12,2);$t->timestamps();});
  Schema::create('deliveries',function(Blueprint $t){$t->id();$t->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();$t->string('shipper_name')->nullable();$t->string('shipper_phone',30)->nullable();$t->enum('status',['waiting','assigned','picked_up','delivered','failed'])->default('waiting');$t->timestamp('picked_up_at')->nullable();$t->timestamp('delivered_at')->nullable();$t->decimal('cod_amount',12,2)->nullable();$t->text('failure_reason')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('deliveries');Schema::dropIfExists('order_items');Schema::dropIfExists('orders');}
};
