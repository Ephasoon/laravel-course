<?php

namespace Database\Seeders;

use App\Models\Maker;
use Illuminate\Database\Seeder;

class MakerSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Toyota', 'Lexus', 'Honda', 'Ford',
            'BMW', 'Mercedes-Benz', 'Hyundai', 'Nissan',
        ] as $name) {
            Maker::firstOrCreate(['name' => $name]);
        }
    }
}
