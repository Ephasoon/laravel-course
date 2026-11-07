<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'state_id' => State::factory(),
            'name' => fake()->randomElement([
                'Addis Ababa', 'Adama', 'Bishoftu', 'Bahir Dar', 'Gondar',
                'Hawassa', 'Mekelle', 'Axum', 'Jigjiga', 'Semera',
            ]),
        ];
    }
}
