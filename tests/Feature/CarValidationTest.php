<?php

use App\Models\Car;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;

test('missing required fields return validation errors', function () {
    $catalog = carValidationCatalog();

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), [])
        ->assertSessionHasErrors([
            'maker_id', 'model_id', 'year', 'car_type_id', 'fuel_type_id',
            'city_id', 'price', 'mileage', 'vin', 'phone',
        ]);
});

test('a model that does not belong to the chosen maker is rejected', function () {
    $catalog = carValidationCatalog();
    $otherMaker = Maker::factory()->create();
    $otherModel = Model::factory()->create(['maker_id' => $otherMaker->id]);

    $payload = validCarPayload($catalog);
    $payload['model_id'] = $otherModel->id;

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), $payload)
        ->assertSessionHasErrors('model_id');
});

test('a duplicate vin is rejected', function () {
    $catalog = carValidationCatalog();

    Car::factory()->create(['vin' => '1HGCM82633A004352']);

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), validCarPayload($catalog))
        ->assertSessionHasErrors('vin');
});

test('a valid payload redirects to car index with a flash message', function () {
    $catalog = carValidationCatalog();

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), validCarPayload($catalog))
        ->assertRedirect(route('car.index'))
        ->assertSessionHas('success');
});

function carValidationCatalog(): array
{
    $maker = Maker::factory()->create();
    $model = Model::factory()->create(['maker_id' => $maker->id]);

    return [
        'maker' => $maker,
        'model' => $model,
        'car_type' => CarType::factory()->create(),
        'fuel_type' => FuelType::factory()->create(),
        'city' => City::factory()->create(),
        'user' => User::factory()->create(),
    ];
}

function validCarPayload(array $catalog): array
{
    return [
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'year' => 2020,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
        'price' => 1000000,
        'mileage' => 50000,
        'vin' => '1HGCM82633A004352',
        'phone' => '0912345678',
        'address' => 'Bole',
        'description' => 'Nice car',
        'published' => 1,
        'abs' => 1,
    ];
}
