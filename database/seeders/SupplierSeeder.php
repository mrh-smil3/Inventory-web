<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier;
class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Supplier A', 'phone' => '123456789', 'address' => '123 Main St'],
            ['name' => 'Supplier B', 'phone' => '987654321', 'address' => '456 Elm St'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
