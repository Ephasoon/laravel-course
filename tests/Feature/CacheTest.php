<?php

use App\Models\Car;
use App\Models\CarImages;
use App\Models\Maker;
use App\Support\Catalog;
use Illuminate\Support\Facades\Cache;

test('visiting the home page caches the latest car ids', function () {
    $this->get('/')->assertOk();

    expect(Cache::has('home.latest_car_ids'))->toBeTrue();
});

test('creating a published car clears the home cache and appears on the next visit', function () {
    $this->get('/')->assertOk();

    expect(Cache::has('home.latest_car_ids'))->toBeTrue();

    $car = Car::factory()->create(['published_at' => now()]);

    expect(Cache::has('home.latest_car_ids'))->toBeFalse();

    $response = $this->get('/');

    $response->assertOk();
    expect($response->viewData('cars')->pluck('id')->all())->toContain($car->id);
});

test('deleting a car removes it from the next home visit', function () {
    $car = Car::factory()->create(['published_at' => now()]);

    $first = $this->get('/');
    expect($first->viewData('cars')->pluck('id')->all())->toContain($car->id);

    $car->delete();

    expect(Cache::has('home.latest_car_ids'))->toBeFalse();

    $second = $this->get('/');
    expect($second->viewData('cars')->pluck('id')->all())->not->toContain($car->id);
});

test('catalog makers are cached and creating a maker clears the cache', function () {
    Catalog::makers();

    expect(Cache::has('catalog.makers'))->toBeTrue();

    $maker = Maker::factory()->create();

    expect(Cache::has('catalog.makers'))->toBeFalse();
    expect(Catalog::makers()->pluck('id')->all())->toContain($maker->id);
});

test('database cache stores plain arrays and catalog still returns models', function () {
    config(['cache.default' => 'database']);

    Maker::factory()->create();

    Catalog::makers();
    $this->get('/')->assertOk();

    expect(Cache::store('database')->get('catalog.makers'))->toBeArray();
    expect(Cache::store('database')->get('home.latest_car_ids'))->toBeArray();

    $this->get('/')->assertOk();

    expect(Catalog::makers()->first())->toBeInstanceOf(Maker::class);
});

test('creating a car image clears the home cache', function () {
    $this->get('/')->assertOk();

    expect(Cache::has('home.latest_car_ids'))->toBeTrue();

    CarImages::factory()->create();

    expect(Cache::has('home.latest_car_ids'))->toBeFalse();
});
