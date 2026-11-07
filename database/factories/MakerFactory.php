<?php

namespace Database\Factories;

use App\Models\Maker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Maker>
 */
class MakerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Toyota', 'Lexus', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz', 'Hyundai', 'Nissan',
            ]),
        ];
    }
}
