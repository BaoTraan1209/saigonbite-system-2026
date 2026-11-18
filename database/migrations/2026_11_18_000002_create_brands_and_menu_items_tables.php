<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('brands',function(Blueprint $t){$t->id();$t->string('name');$t->text('description')->nullable();$t->enum('status',['active','inactive','archived'])->default('active');$t->timestamps();});
  Schema::create('menu_items',function(Blueprint $t){$t->id();$t->foreignId('brand_id')->constrained()->cascadeOnDelete();$t->string('name');$t->text('description')->nullable();$t->decimal('price',12,2);$t->boolean('is_available')->default(true);$t->timestamps();});
  Schema::create('hub_brands',function(Blueprint $t){$t->id();$t->foreignId('hub_id')->constrained()->cascadeOnDelete();$t->foreignId('brand_id')->constrained()->cascadeOnDelete();$t->unique(['hub_id','brand_id']);});
 }
 public function down(): void { Schema::dropIfExists('hub_brands');Schema::dropIfExists('menu_items');Schema::dropIfExists('brands'); }
};
