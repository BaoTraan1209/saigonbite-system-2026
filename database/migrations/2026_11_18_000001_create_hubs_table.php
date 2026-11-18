<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void { Schema::create('hubs', function(Blueprint $t){$t->id();$t->string('name');$t->text('address');$t->string('phone',30)->nullable();$t->unsignedInteger('capacity');$t->enum('status',['draft','active','suspending','suspended','archived'])->default('draft');$t->timestamps();});}
 public function down(): void { Schema::dropIfExists('hubs'); }
};
