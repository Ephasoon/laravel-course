<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuelTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Gas', 'Diesel', 'Electric', 'Hybrid'] as $name) {
            DB::table('fuel_types')->updateOrInsert(['name' => $name], ['name' => $name]);
        }
    }
}
