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
use Illuminate\Support\Carbon;

test('every model exposes the expected fillable attributes', function () {
    expect((new Car)->getFillable())->toContain(
        'maker_id', 'model_id', 'year', 'price', 'vin', 'mileage',
        'car_type_id', 'fuel_type_id', 'user_id', 'city_id', 'address',
        'phone', 'description', 'published_at'
    );

    expect((new CarFeatures)->getFillable())->toContain('car_id', 'abs', 'leather_seats');
    expect((new CarImages)->getFillable())->toContain('car_id', 'image_path', 'position');
    expect((new CarType)->getFillable())->toContain('name');
    expect((new City)->getFillable())->toContain('state_id', 'name');
    expect((new FuelType)->getFillable())->toContain('name');
    expect((new Maker)->getFillable())->toContain('name');
    expect((new Model)->getFillable())->toContain('maker_id', 'name');
    expect((new State)->getFillable())->toContain('name');

    expect((new User)->getFillable())->toContain(
        'name', 'email', 'password', 'phone', 'google_id', 'facebook_id'
    );
});

test('car casts published_at, price, year and mileage', function () {
    $car = new Car([
        'published_at' => '2026-01-02 03:04:05',
        'price' => '1500000',
        'year' => '2019',
        'mileage' => '45000',
    ]);

    expect($car->published_at)->toBeInstanceOf(Carbon::class)
        ->and($car->price)->toBeInt()->toBe(1500000)
        ->and($car->year)->toBeInt()->toBe(2019)
        ->and($car->mileage)->toBeInt()->toBe(45000);
});

test('car features casts booleans and uses car_id as its non incrementing key', function () {
    $features = new CarFeatures(['abs' => 1, 'leather_seats' => 0]);

    expect($features->getKeyName())->toBe('car_id')
        ->and($features->incrementing)->toBeFalse()
        ->and($features->usesTimestamps())->toBeTrue()
        ->and($features->abs)->toBeTrue()
        ->and($features->leather_seats)->toBeFalse();
});

test('car images does not use timestamps', function () {
    expect((new CarImages)->usesTimestamps())->toBeFalse();
});
