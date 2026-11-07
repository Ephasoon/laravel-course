<?php

use App\Models\Car;
use App\Models\CarFeatures;
use App\Models\CarImages;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

test('database seeder creates 30 published cars with features and images for five users', function () {
    $this->seed();

    expect(Car::count())->toBe(30)
        ->and(Car::whereNotNull('published_at')->count())->toBe(30)
        ->and(Car::distinct()->count('user_id'))->toBe(5)
        ->and(Car::has('features')->count())->toBe(30)
        ->and(CarFeatures::count())->toBe(30)
        ->and(CarImages::count())->toBeGreaterThanOrEqual(90)
        ->and(CarImages::count())->toBeLessThanOrEqual(150);

    foreach (Car::withCount('images')->get() as $car) {
        expect($car->images_count)->toBeGreaterThanOrEqual(3)
            ->toBeLessThanOrEqual(5);
    }
});

test('seeded car images point to files that exist under public', function () {
    $this->seed();

    foreach (CarImages::all() as $image) {
        expect(public_path(ltrim($image->image_path, '/')))->toBeFile();
    }
});

test('database seeder creates the known login user and random favourites', function () {
    $this->seed();

    $user = User::where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull()
        ->and(Hash::check('password', $user->password))->toBeTrue()
        ->and(DB::table('favourite_cars')->count())->toBeGreaterThan(0)
        ->and($user->favouriteCars->count())->toBeGreaterThanOrEqual(3);
});
