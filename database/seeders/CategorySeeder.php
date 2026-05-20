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
            ['name' => 'Makanan', 'slug' => 'makanan'],
            ['name' => 'Minuman', 'slug' => 'minuman'],
            ['name' => 'Bahan Pokok', 'slug' => 'bahan-pokok'],
            ['name' => 'Peralatan', 'slug' => 'peralatan'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
