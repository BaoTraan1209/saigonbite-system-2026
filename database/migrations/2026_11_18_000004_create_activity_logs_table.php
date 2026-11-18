<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {Schema::create('activity_logs',function(Blueprint $t){$t->id();$t->string('actor')->nullable();$t->string('action');$t->string('subject_type');$t->unsignedBigInteger('subject_id');$t->json('details')->nullable();$t->timestamp('created_at')->useCurrent();});}
 public function down(): void {Schema::dropIfExists('activity_logs');}
};
