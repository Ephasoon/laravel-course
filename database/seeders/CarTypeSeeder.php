<?php

namespace Database\Seeders;

use App\Models\CarType;
use Illuminate\Database\Seeder;

class CarTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Sedan', 'SUV', 'Hatchback', 'Pickup', 'Coupe', 'Van'] as $name) {
            CarType::firstOrCreate(['name' => $name]);
        }
    }
}
