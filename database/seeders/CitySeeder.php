<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            'Addis Ababa' => ['Addis Ababa'],
            'Oromia' => ['Adama', 'Bishoftu', 'Jimma', 'Nekemte'],
            'Amhara' => ['Bahir Dar', 'Gondar', 'Dessie', 'Debre Markos'],
            'Sidama' => ['Hawassa', 'Bensa', 'Aleta Wondo'],
            'Tigray' => ['Mekelle', 'Axum', 'Adigrat'],
            'Somali' => ['Jigjiga', 'Gode'],
            'Afar' => ['Semera', 'Asaita'],
            'Dire Dawa' => ['Dire Dawa'],
        ];

        foreach ($cities as $stateName => $names) {
            $state = State::where('name', $stateName)->firstOrFail();

            foreach ($names as $name) {
                City::firstOrCreate([
                    'state_id' => $state->id,
                    'name' => $name,
                ]);
            }
        }
    }
}
