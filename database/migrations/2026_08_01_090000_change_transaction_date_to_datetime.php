<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE stock_ins MODIFY transaction_date DATETIME NOT NULL');
        DB::statement('ALTER TABLE stock_outs MODIFY transaction_date DATETIME NOT NULL');
        DB::statement('ALTER TABLE stock_mutations MODIFY transaction_date DATETIME NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE stock_ins MODIFY transaction_date DATE NOT NULL');
        DB::statement('ALTER TABLE stock_outs MODIFY transaction_date DATE NOT NULL');
        DB::statement('ALTER TABLE stock_mutations MODIFY transaction_date DATE NOT NULL');
    }
};
