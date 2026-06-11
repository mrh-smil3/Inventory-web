<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Beras Rojo Lele',
                'sku' => 'BRS-RL-50KG',
                'purchase_price' => 50000,
                'selling_price' => 60000,
                'stock' => 0,
                'category_id' => 3,
                'unit_id' => 6,
            ],
            [
                'name' => 'Minyak Goreng Sania',
                'sku' => 'MIN-1L-SN',
                'purchase_price' => 18000,
                'selling_price' => 20000,
                'stock' => 0,
                'category_id' => 3,
                'unit_id' => 5,
            ],
            [
                'name' => 'Telur Ayam Negeri',
                'sku' => 'TLR-AN',
                'purchase_price' => 3000,
                'selling_price' => 3500,
                'stock' => 0,
                'category_id' => 3,
                'unit_id' => 1,
            ],
        ];

        Product::insert($products);
    }
}
