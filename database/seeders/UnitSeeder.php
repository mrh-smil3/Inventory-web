<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pcs'],
            ['name' => 'Box'],
            ['name' => 'Dus'],
            ['name' => 'Rol'],
            ['name' => 'Liter'],
            ['name' => 'Kg'],

        ];

        foreach ($units as $unit) {
            Unit::create($unit);
        }
    }
}
