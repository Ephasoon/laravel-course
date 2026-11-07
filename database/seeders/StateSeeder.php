<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Addis Ababa', 'Oromia', 'Amhara', 'Sidama',
            'Tigray', 'Somali', 'Afar', 'Dire Dawa',
        ] as $name) {
            State::firstOrCreate(['name' => $name]);
        }
    }
}
