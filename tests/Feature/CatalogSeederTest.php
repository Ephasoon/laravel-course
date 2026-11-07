<?php

use App\Models\CarType;
use App\Models\City;
use App\Models\Maker;
use App\Models\Model;
use App\Models\State;
use Database\Seeders\CarTypeSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\MakerSeeder;
use Database\Seeders\ModelSeeder;
use Database\Seeders\StateSeeder;

test('catalog seeders create realistic makers, models, types, states and cities', function () {
    $this->seed([
        CarTypeSeeder::class,
        MakerSeeder::class,
        ModelSeeder::class,
        StateSeeder::class,
        CitySeeder::class,
    ]);

    expect(Maker::count())->toBe(8)
        ->and(Maker::whereIn('name', [
            'Toyota', 'Lexus', 'Honda', 'Ford',
            'BMW', 'Mercedes-Benz', 'Hyundai', 'Nissan',
        ])->count())->toBe(8);

    foreach (Maker::with('models')->get() as $maker) {
        expect($maker->models->count())->toBeGreaterThanOrEqual(3)
            ->toBeLessThanOrEqual(5);
    }

    expect(CarType::pluck('name')->all())->toContain('Sedan', 'SUV', 'Hatchback', 'Pickup', 'Coupe', 'Van');

    expect(State::pluck('name')->all())->toContain(
        'Addis Ababa', 'Oromia', 'Amhara', 'Sidama', 'Tigray'
    );

    expect(City::where('name', 'Hawassa')->first()->state->name)->toBe('Sidama')
        ->and(City::where('name', 'Adama')->first()->state->name)->toBe('Oromia')
        ->and(City::where('name', 'Mekelle')->first()->state->name)->toBe('Tigray');
});

test('catalog factories create records with the correct relationships', function () {
    $maker = Maker::factory()->create();
    $model = Model::factory()->create();
    $state = State::factory()->create();
    $city = City::factory()->create();
    $carType = CarType::factory()->create();

    expect($maker->models)->toHaveCount(0)
        ->and($model->maker)->toBeInstanceOf(Maker::class)
        ->and($city->state)->toBeInstanceOf(State::class)
        ->and($state->cities)->toHaveCount(0)
        ->and($carType->name)->not->toBeEmpty();
});
