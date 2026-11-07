<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Addis Ababa', 'Oromia', 'Amhara', 'Sidama', 'Tigray', 'Somali', 'Afar', 'Dire Dawa',
            ]),
        ];
    }
}
