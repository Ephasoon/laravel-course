<?php

namespace Database\Factories;

use App\Models\Maker;
use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class ModelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'maker_id' => Maker::factory(),
            'name' => fake()->randomElement([
                'Corolla', 'Camry', 'RAV4', 'Civic', 'Accord', 'F-150', 'Mustang',
                '3 Series', 'X5', 'C-Class', 'Elantra', 'Tucson', 'Altima', 'Rogue',
            ]),
        ];
    }
}
