<?php

namespace Database\Factories;

use App\Models\Car;
use App\Models\CarFeatures;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarFeatures>
 */
class CarFeaturesFactory extends Factory
{
    public function definition(): array
    {
        return [
            'car_id' => Car::factory(),
            'abs' => fake()->boolean(70),
            'air_conditioning' => fake()->boolean(80),
            'power_windows' => fake()->boolean(85),
            'power_door_locks' => fake()->boolean(85),
            'cruise_control' => fake()->boolean(50),
            'bluetooth_connectivity' => fake()->boolean(70),
            'remote_start' => fake()->boolean(35),
            'gps_navigation' => fake()->boolean(45),
            'heated_seats' => fake()->boolean(40),
            'climate_control' => fake()->boolean(55),
            'rear_parking_sensors' => fake()->boolean(50),
            'leather_seats' => fake()->boolean(45),
        ];
    }
}
