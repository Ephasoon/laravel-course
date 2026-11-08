<?php

use App\Models\Car;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;

test('home page returns 200 and shows a seeded car maker', function () {
    $car = Car::factory()->create(['published_at' => now()]);

    $this->get('/')
        ->assertOk()
        ->assertSee($car->maker->name)
        ->assertSee(route('car.show', $car));
});

test('search filters by maker and returns only that maker cars', function () {
    $makerA = Maker::factory()->create(['name' => 'Toyota']);
    $makerB = Maker::factory()->create(['name' => 'Honda']);
    $modelA = Model::factory()->create(['maker_id' => $makerA->id, 'name' => 'Corolla']);
    $modelB = Model::factory()->create(['maker_id' => $makerB->id, 'name' => 'Civic']);

    $carA = Car::factory()->create(['maker_id' => $makerA->id, 'model_id' => $modelA->id]);
    Car::factory()->create(['maker_id' => $makerB->id, 'model_id' => $modelB->id]);

    $response = $this->get(route('car.search', ['maker_id' => $makerA->id]));

    $response->assertOk();

    expect($response->viewData('cars')->pluck('id')->all())->toBe([$carA->id]);
});

test('search filters by price range', function () {
    $catalog = searchCatalog();

    $cheap = Car::factory()->create([...$catalog, 'price' => 100]);
    $middle = Car::factory()->create([...$catalog, 'price' => 500]);
    $expensive = Car::factory()->create([...$catalog, 'price' => 1000]);

    $response = $this->get(route('car.search', [
        'price_from' => 400,
        'price_to' => 600,
    ]));

    $response->assertOk();

    expect($response->viewData('cars')->pluck('id')->all())->toBe([$middle->id]);
});

test('search paginates published cars 15 per page', function () {
    $catalog = searchCatalog();

    Car::factory()->count(20)->create($catalog);

    $firstPage = $this->get(route('car.search'))->viewData('cars');
    $secondPage = $this->get(route('car.search', ['page' => 2]))->viewData('cars');

    expect($firstPage->count())->toBe(15)
        ->and($firstPage->lastPage())->toBe(2)
        ->and($secondPage->count())->toBe(5);
});

function searchCatalog(): array
{
    $maker = Maker::factory()->create();
    $model = Model::factory()->create(['maker_id' => $maker->id]);

    return [
        'maker_id' => $maker->id,
        'model_id' => $model->id,
        'car_type_id' => CarType::factory()->create()->id,
        'fuel_type_id' => FuelType::factory()->create()->id,
        'user_id' => User::factory()->create()->id,
        'city_id' => City::factory()->create()->id,
    ];
}
