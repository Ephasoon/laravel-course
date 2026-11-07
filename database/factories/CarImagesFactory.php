<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarImages;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarImages>
 */
class CarImagesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'image_path' => '/img/cars/Lexus-RX200t-2016/'.fake()->numberBetween(1, 7).'.jpeg',
            'position' => fake()->numberBetween(1, 5),
        ];
    }
}
