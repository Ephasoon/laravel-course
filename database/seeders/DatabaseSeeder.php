<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            FuelTypeSeeder::class,
            CarTypeSeeder::class,
            MakerSeeder::class,
            ModelSeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            CarSeeder::class,
            FavouriteCarSeeder::class,
        ]);

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
            ]
        );
    }
}
