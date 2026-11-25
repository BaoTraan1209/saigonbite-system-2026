<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 30)
                ->default('COD')
                ->change();

            $table->decimal('delivery_fee', 12, 2)
                ->default(0)
                ->after('subtotal');

            $table->enum('payment_status', [
                'unpaid',
                'paid',
            ])->default('unpaid')
                ->after('payment_method');
        });
    }

    public function down(): void
    {
        DB::table('orders')
            ->where('payment_method', 'COD')
            ->update(['payment_method' => 'cash']);
        DB::table('orders')
            ->where('payment_method', 'CARD')
            ->update(['payment_method' => 'card']);
        DB::table('orders')
            ->where('payment_method', 'ONLINE')
            ->update(['payment_method' => 'online']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_fee',
                'payment_status',
            ]);

            $table->enum('payment_method', [
                'cash',
                'card',
                'online',
            ])->default('cash')->change();
        });
    }
};
