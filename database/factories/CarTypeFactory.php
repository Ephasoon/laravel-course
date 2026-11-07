<?php

namespace Database\Factories;

use App\Models\CarType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarType>
 */
class CarTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Sedan', 'SUV', 'Hatchback', 'Pickup', 'Coupe', 'Van']),
        ];
    }
}
