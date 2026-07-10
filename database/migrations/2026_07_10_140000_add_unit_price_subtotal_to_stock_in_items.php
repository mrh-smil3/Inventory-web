<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_in_items', function (Blueprint $table) {
            $table->decimal('unit_price', 12, 2)->after('product_id')->nullable();
            $table->decimal('subtotal', 12, 2)->after('quantity')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('stock_in_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'subtotal']);
        });
    }
};
