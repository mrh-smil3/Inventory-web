<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });

        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->foreignId('stock_in_item_id')
                ->nullable()
                ->after('reference_id')
                ->constrained()
                ->nullOnDelete();
            $table->foreignId('stock_out_item_id')
                ->nullable()
                ->after('stock_in_item_id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('stock_mutations')
            ->where('type', 'in')
            ->whereNull('stock_in_item_id')
            ->update([
                'stock_in_item_id' => DB::raw('reference_id'),
            ]);

        $now = now();

        DB::table('stock_outs')
            ->orderBy('id')
            ->chunkById(100, function ($stockOuts) use ($now) {
                foreach ($stockOuts as $stockOut) {
                    DB::table('stock_out_items')->insert([
                        'stock_out_id' => $stockOut->id,
                        'product_id' => $stockOut->product_id,
                        'quantity' => $stockOut->quantity,
                        'created_at' => $stockOut->created_at ?? $now,
                        'updated_at' => $stockOut->updated_at ?? $now,
                    ]);
                }
            });

        DB::table('stock_mutations')
            ->where('type', 'out')
            ->delete();

        DB::table('stock_out_items')
            ->join('stock_outs', 'stock_out_items.stock_out_id', '=', 'stock_outs.id')
            ->select([
                'stock_out_items.id',
                'stock_out_items.product_id',
                'stock_out_items.quantity',
                'stock_outs.transaction_date',
                'stock_outs.note',
                'stock_out_items.created_at',
                'stock_out_items.updated_at',
            ])
            ->orderBy('stock_out_items.id')
            ->chunk(100, function ($items) {
                foreach ($items as $item) {
                    DB::table('stock_mutations')->insert([
                        'product_id' => $item->product_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'transaction_date' => $item->transaction_date,
                        'reference_id' => -$item->id,
                        'stock_out_item_id' => $item->id,
                        'note' => $item->note,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ]);
                }
            });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::statement('ALTER TABLE stock_outs MODIFY product_id BIGINT UNSIGNED NULL');

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_mutations', function (Blueprint $table) {
            $table->dropForeign(['stock_in_item_id']);
            $table->dropForeign(['stock_out_item_id']);
            $table->dropColumn(['stock_in_item_id', 'stock_out_item_id']);
        });

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::table('stock_outs')
            ->whereNull('product_id')
            ->update([
                'product_id' => DB::raw('(select product_id from stock_out_items where stock_out_items.stock_out_id = stock_outs.id order by id limit 1)'),
            ]);

        DB::statement('ALTER TABLE stock_outs MODIFY product_id BIGINT UNSIGNED NOT NULL');

        Schema::table('stock_outs', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        Schema::dropIfExists('stock_out_items');
    }
};
