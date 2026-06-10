<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'description' => 'Kategori Makanan'],
            ['name' => 'Minuman', 'description' => 'Kategori Minuman'],
            ['name' => 'Bahan Pokok', 'description' => 'Kategori Bahan Pokok'],
            ['name' => 'Peralatan', 'description' => 'Kategori Peralatan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
