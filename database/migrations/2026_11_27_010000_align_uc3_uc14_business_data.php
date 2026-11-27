<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('is_available');
        });

        Schema::create('hub_menu_items', function (Blueprint $table) {
            $table->foreignId('hub_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_available')->default(false);
            $table->primary(['hub_id', 'menu_item_id']);
            $table->index(['hub_id', 'is_available']);
        });

        // Preserve existing global availability as the initial state at each assigned Hub.
        DB::table('hub_brands')
            ->join('menu_items', 'menu_items.brand_id', '=', 'hub_brands.brand_id')
            ->select('hub_brands.hub_id', 'menu_items.id', 'menu_items.is_available')
            ->orderBy('hub_brands.hub_id')
            ->orderBy('menu_items.id')
            ->get()
            ->each(function ($row): void {
                DB::table('hub_menu_items')->insert([
                    'hub_id' => $row->hub_id,
                    'menu_item_id' => $row->id,
                    'is_available' => (bool) $row->is_available,
                ]);
            });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name', 150);
            $table->string('phone', 30);
            $table->string('street', 255);
            $table->string('district', 150);
            $table->string('city', 150);
            $table->string('additional_details', 500)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->index(['user_id', 'is_default']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('hub_id')->constrained()->nullOnDelete();
            $table->string('brand_name', 255)->nullable()->after('brand_id');
            $table->foreignId('customer_address_id')->nullable()->after('customer_id')
                ->constrained('customer_addresses')->nullOnDelete();
            $table->string('voucher_code', 100)->nullable()->after('customer_address_id');
            $table->decimal('surcharge', 12, 2)->default(0)->after('delivery_fee');
            $table->uuid('idempotency_key')->nullable()->unique();
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->decimal('cash_collected', 12, 2)->nullable()->after('cod_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropConstrainedForeignId('customer_address_id');
            $table->dropConstrainedForeignId('brand_id');
            $table->dropColumn(['brand_name', 'voucher_code', 'surcharge', 'idempotency_key']);
        });

        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropColumn('cash_collected');
        });

        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('hub_menu_items');

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
