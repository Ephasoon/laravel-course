<?php

use App\Models\Car;
use App\Models\CarFeatures;
use App\Models\CarImages;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('store creates a car owned by the user with features and images', function () {
    Storage::fake('public');

    $catalog = carCrudCatalog();

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), carCrudPayload($catalog, [
            'published' => 1,
            'abs' => 1,
            'images' => [
                UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
                UploadedFile::fake()->create('b.jpg', 100, 'image/jpeg'),
            ],
        ]))
        ->assertRedirect(route('car.index'));

    $car = Car::where('vin', '1HGCM82633A004352')->firstOrFail();

    expect($car->user_id)->toBe($catalog['user']->id);
    expect($car->published_at)->not->toBeNull();
    expect($car->features->abs)->toBeTrue();
    expect($car->features->air_conditioning)->toBeFalse();
    expect($car->images)->toHaveCount(2);
    expect($car->images->pluck('position')->all())->toBe([1, 2]);
    expect($car->images->first()->image_path)->toStartWith('/storage/cars/'.$car->id.'/');
    expect(Storage::disk('public')->allFiles('cars/'.$car->id))->toHaveCount(2);
});

test('unchecked features are saved as false and unpublished cars get a null published_at', function () {
    Storage::fake('public');

    $catalog = carCrudCatalog();

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), carCrudPayload($catalog))
        ->assertRedirect(route('car.index'));

    $car = Car::where('vin', '1HGCM82633A004352')->firstOrFail();

    expect($car->published_at)->toBeNull();
    expect($car->features->abs)->toBeFalse();
    expect($car->features->air_conditioning)->toBeFalse();
});

test('update changes the price', function () {
    Storage::fake('public');

    $catalog = carCrudCatalog();
    $car = Car::factory()->create([
        'user_id' => $catalog['user']->id,
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
        'price' => 500000,
        'published_at' => null,
    ]);

    $this->actingAs($catalog['user'])
        ->put(route('car.update', $car), carCrudPayload($catalog, [
            'price' => 900000,
            'published' => 1,
        ]))
        ->assertRedirect(route('car.index'));

    $car->refresh();

    expect($car->price)->toBe(900000);
    expect($car->published_at)->not->toBeNull();
});

test('update with delete_images removes only that car image', function () {
    Storage::fake('public');

    $catalog = carCrudCatalog();
    $car = Car::factory()->create([
        'user_id' => $catalog['user']->id,
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
    ]);

    Storage::disk('public')->put('cars/'.$car->id.'/one.jpg', 'one');
    Storage::disk('public')->put('cars/'.$car->id.'/two.jpg', 'two');

    $first = CarImages::factory()->create([
        'car_id' => $car->id,
        'image_path' => '/storage/cars/'.$car->id.'/one.jpg',
        'position' => 1,
    ]);
    $second = CarImages::factory()->create([
        'car_id' => $car->id,
        'image_path' => '/storage/cars/'.$car->id.'/two.jpg',
        'position' => 2,
    ]);

    $this->actingAs($catalog['user'])
        ->put(route('car.update', $car), carCrudPayload($catalog, [
            'delete_images' => [$first->id],
        ]))
        ->assertRedirect(route('car.index'));

    $this->assertDatabaseMissing('car_images', ['id' => $first->id]);
    $this->assertDatabaseHas('car_images', ['id' => $second->id, 'position' => 1]);
    Storage::disk('public')->assertMissing('cars/'.$car->id.'/one.jpg');
    Storage::disk('public')->assertExists('cars/'.$car->id.'/two.jpg');
});

test('a user cannot delete another car image via delete_images', function () {
    Storage::fake('public');

    $catalog = carCrudCatalog();
    $car = Car::factory()->create([
        'user_id' => $catalog['user']->id,
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
    ]);

    $otherCar = Car::factory()->create();
    Storage::disk('public')->put('cars/'.$otherCar->id.'/other.jpg', 'other');

    $otherImage = CarImages::factory()->create([
        'car_id' => $otherCar->id,
        'image_path' => '/storage/cars/'.$otherCar->id.'/other.jpg',
        'position' => 1,
    ]);

    $this->actingAs($catalog['user'])
        ->put(route('car.update', $car), carCrudPayload($catalog, [
            'delete_images' => [$otherImage->id],
        ]))
        ->assertRedirect(route('car.index'));

    $this->assertDatabaseHas('car_images', ['id' => $otherImage->id]);
    Storage::disk('public')->assertExists('cars/'.$otherCar->id.'/other.jpg');
});

function carCrudCatalog(): array
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

function carCrudPayload(array $catalog, array $overrides = []): array
{
    return array_merge([
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
    ], $overrides);
}
