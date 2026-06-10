<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_in_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });

        $now = now();

        DB::table('stock_ins')
            ->orderBy('id')
            ->chunkById(100, function ($stockIns) use ($now) {
                foreach ($stockIns as $stockIn) {
                    DB::table('stock_in_items')->insert([
                        'stock_in_id' => $stockIn->id,
                        'product_id' => $stockIn->product_id,
                        'quantity' => $stockIn->quantity,
                        'created_at' => $stockIn->created_at ?? $now,
                        'updated_at' => $stockIn->updated_at ?? $now,
                    ]);
                }
            });

        DB::table('stock_mutations')
            ->where('type', 'in')
            ->delete();

        DB::table('stock_in_items')
            ->join('stock_ins', 'stock_in_items.stock_in_id', '=', 'stock_ins.id')
            ->select([
                'stock_in_items.id',
                'stock_in_items.product_id',
                'stock_in_items.quantity',
                'stock_ins.transaction_date',
                'stock_ins.note',
                'stock_in_items.created_at',
                'stock_in_items.updated_at',
            ])
            ->orderBy('stock_in_items.id')
            ->chunk(100, function ($items) {
                foreach ($items as $item) {
                    DB::table('stock_mutations')->insert([
                        'product_id' => $item->product_id,
                        'type' => 'in',
                        'quantity' => $item->quantity,
                        'transaction_date' => $item->transaction_date,
                        'reference_id' => $item->id,
                        'note' => $item->note,
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ]);
                }
            });

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::statement('ALTER TABLE stock_ins MODIFY product_id BIGINT UNSIGNED NULL');

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
        });

        DB::table('stock_ins')
            ->whereNull('product_id')
            ->update([
                'product_id' => DB::raw('(select product_id from stock_in_items where stock_in_items.stock_in_id = stock_ins.id order by id limit 1)'),
            ]);

        DB::statement('ALTER TABLE stock_ins MODIFY product_id BIGINT UNSIGNED NOT NULL');

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });

        Schema::dropIfExists('stock_in_items');
    }
};
