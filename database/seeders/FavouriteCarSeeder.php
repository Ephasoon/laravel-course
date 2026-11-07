<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavouriteCarSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereIn('email', [
            'test@example.com',
            'test2@example.com',
            'test3@example.com',
            'test4@example.com',
            'test5@example.com',
        ])->get();

        $carIds = Car::pluck('id');

        if ($carIds->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            foreach ($carIds->random(rand(3, 8)) as $carId) {
                DB::table('favourite_cars')->updateOrInsert([
                    'user_id' => $user->id,
                    'car_id' => $carId,
                ]);
            }
        }
    }
}
