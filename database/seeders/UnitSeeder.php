<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs', 'slug' => 'pcs'],
            ['name' => 'Box', 'slug' => 'box'],
            ['name' => 'Dus', 'slug' => 'dus'],
            ['name' => 'Rol', 'slug' => 'rol'],
            ['name' => 'Liter', 'slug' => 'liter'],
            ['name' => 'Kg', 'slug' => 'kg'],
            
        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
