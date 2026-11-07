<?php

namespace Database\Factories;

use App\Models\FuelType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FuelType>
 */
class FuelTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Gas', 'Diesel', 'Electric', 'Hybrid']),
        ];
    }
}
