<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'maker_id' => Maker::factory(),
            'model_id' => Model::factory(),
            'year' => fake()->numberBetween(2005, 2024),
            'price' => fake()->numberBetween(500000, 8000000),
            'vin' => strtoupper(fake()->unique()->bothify('VIN########')),
            'mileage' => fake()->numberBetween(0, 250000),
            'car_type_id' => CarType::factory(),
            'fuel_type_id' => FuelType::factory(),
            'user_id' => User::factory(),
            'city_id' => City::factory(),
            'address' => fake()->streetAddress(),
            'phone' => fake()->numerify('09########'),
            'description' => fake()->paragraph(),
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
