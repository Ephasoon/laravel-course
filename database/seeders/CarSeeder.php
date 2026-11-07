<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarFeatures;
use App\Models\CarImages;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $users = collect([
            ['name' => 'Test User', 'email' => 'test@example.com'],
            ['name' => 'Test User 2', 'email' => 'test2@example.com'],
            ['name' => 'Test User 3', 'email' => 'test3@example.com'],
            ['name' => 'Test User 4', 'email' => 'test4@example.com'],
            ['name' => 'Test User 5', 'email' => 'test5@example.com'],
        ])->map(fn (array $user) => User::updateOrCreate(
            ['email' => $user['email']],
            ['name' => $user['name'], 'password' => Hash::make('password')]
        ));

        if (Car::count() >= 30) {
            return;
        }

        $makers = Maker::with('models')->get();
        $carTypes = CarType::all();
        $fuelTypes = FuelType::all();
        $cities = City::all();

        for ($i = 0; $i < 30; $i++) {
            $maker = $makers->random();
            $model = $maker->models->random();
            $city = $cities->random();

            $car = Car::factory()->create([
                'maker_id' => $maker->id,
                'model_id' => $model->id,
                'car_type_id' => $carTypes->random()->id,
                'fuel_type_id' => $fuelTypes->random()->id,
                'city_id' => $city->id,
                'user_id' => $users[$i % $users->count()]->id,
                'published_at' => now()->subDays(rand(0, 365)),
            ]);

            CarFeatures::factory()->create(['car_id' => $car->id]);

            $imageCount = rand(3, 5);

            for ($position = 1; $position <= $imageCount; $position++) {
                CarImages::factory()->create([
                    'car_id' => $car->id,
                    'image_path' => '/img/cars/Lexus-RX200t-2016/'.$position.'.jpeg',
                    'position' => $position,
                ]);
            }
        }
    }
}
