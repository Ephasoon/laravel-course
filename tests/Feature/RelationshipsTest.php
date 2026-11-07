<?php

use App\Models\Car;
use App\Models\CarFeatures;
use App\Models\CarImages;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\State;
use App\Models\User;

function relationshipFixtures(): array
{
    $maker = Maker::create(['name' => 'Toyota']);
    $model = Model::create(['maker_id' => $maker->id, 'name' => 'Corolla']);
    $carType = CarType::create(['name' => 'Sedan']);
    $fuelType = FuelType::create(['name' => 'Gas']);
    $state = State::create(['name' => 'Oromia']);
    $city = City::create(['state_id' => $state->id, 'name' => 'Adama']);
    $user = User::create([
        'name' => 'Owner',
        'email' => 'owner@example.com',
        'password' => 'password',
    ]);

    $car = Car::create([
        'maker_id' => $maker->id,
        'model_id' => $model->id,
        'year' => 2020,
        'price' => 1500000,
        'vin' => 'VIN-REL-001',
        'mileage' => 45000,
        'car_type_id' => $carType->id,
        'fuel_type_id' => $fuelType->id,
        'user_id' => $user->id,
        'city_id' => $city->id,
        'address' => 'Bole Road',
        'phone' => '0911000000',
        'description' => 'A clean sedan.',
        'published_at' => now(),
    ]);

    $features = CarFeatures::create([
        'car_id' => $car->id,
        'abs' => true,
        'air_conditioning' => true,
    ]);

    $image = CarImages::create([
        'car_id' => $car->id,
        'image_path' => '/img/cars/Lexus-RX200t-2016/1.jpeg',
        'position' => 1,
    ]);

    $user->favouriteCars()->attach($car->id);

    return compact(
        'maker', 'model', 'carType', 'fuelType', 'state', 'city',
        'user', 'car', 'features', 'image'
    );
}

test('car belongs to its catalog records and owner', function () {
    $f = relationshipFixtures();

    expect($f['car']->maker->is($f['maker']))->toBeTrue()
        ->and($f['car']->model->is($f['model']))->toBeTrue()
        ->and($f['car']->carType->is($f['carType']))->toBeTrue()
        ->and($f['car']->fuelType->is($f['fuelType']))->toBeTrue()
        ->and($f['car']->user->is($f['user']))->toBeTrue()
        ->and($f['car']->city->is($f['city']))->toBeTrue();
});

test('car has one features record and many images', function () {
    $f = relationshipFixtures();

    expect($f['car']->features->is($f['features']))->toBeTrue()
        ->and($f['car']->images)->toHaveCount(1)
        ->and($f['car']->images->first()->is($f['image']))->toBeTrue();
});

test('user has many cars and favourite cars', function () {
    $f = relationshipFixtures();

    expect($f['user']->cars)->toHaveCount(1)
        ->and($f['user']->cars->first()->is($f['car']))->toBeTrue()
        ->and($f['user']->favouriteCars)->toHaveCount(1)
        ->and($f['user']->favouriteCars->first()->is($f['car']))->toBeTrue();
});

test('maker, model, state, city, car type and fuel type expose inverse relations', function () {
    $f = relationshipFixtures();

    expect($f['maker']->models)->toHaveCount(1)
        ->and($f['maker']->models->first()->is($f['model']))->toBeTrue()
        ->and($f['model']->maker->is($f['maker']))->toBeTrue()
        ->and($f['model']->cars)->toHaveCount(1)
        ->and($f['state']->cities)->toHaveCount(1)
        ->and($f['state']->cities->first()->is($f['city']))->toBeTrue()
        ->and($f['city']->state->is($f['state']))->toBeTrue()
        ->and($f['city']->cars)->toHaveCount(1)
        ->and($f['carType']->cars)->toHaveCount(1)
        ->and($f['fuelType']->cars)->toHaveCount(1);
});

test('features and images belong to their car', function () {
    $f = relationshipFixtures();

    expect($f['features']->car->is($f['car']))->toBeTrue()
        ->and($f['image']->car->is($f['car']))->toBeTrue();
});
