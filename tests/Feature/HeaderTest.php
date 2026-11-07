<?php

use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;

test('guests see login and signup on the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Login')
        ->assertSee('Signup');
});

test('logged in users see their name, my cars and a logout form', function () {
    $user = User::factory()->create(['name' => 'Alice Example']);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Alice Example')
        ->assertSee('My Cars')
        ->assertSee('Add new Car')
        ->assertSee('action="'.route('logout').'"', false)
        ->assertSee('Logout');
});

test('the success flash message is visible after storing a car', function () {
    $user = User::factory()->create();
    $maker = Maker::factory()->create();
    $model = Model::factory()->create(['maker_id' => $maker->id]);
    $carType = CarType::factory()->create();
    $fuelType = FuelType::factory()->create();
    $city = City::factory()->create();

    $this->actingAs($user)
        ->post(route('car.store'), [
            'maker_id' => $maker->id,
            'model_id' => $model->id,
            'year' => 2020,
            'car_type_id' => $carType->id,
            'fuel_type_id' => $fuelType->id,
            'city_id' => $city->id,
            'price' => 1000000,
            'mileage' => 50000,
            'vin' => '1HGCM82633A004352',
            'phone' => '0912345678',
            'address' => 'Bole',
            'description' => 'Nice car',
            'published' => 1,
            'abs' => 1,
        ])
        ->assertRedirect(route('car.index'));

    $this->get(route('car.index'))
        ->assertSee('Car created successfully.');
});
