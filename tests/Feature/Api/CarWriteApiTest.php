<?php

use App\Models\Car;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

function apiCarCatalog(): array
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

function apiCarPayload(array $catalog, array $overrides = []): array
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
        'vin' => strtoupper(fake()->unique()->bothify('API##########')),
        'phone' => '0912345678',
        'address' => 'Bole',
        'description' => 'Nice car',
    ], $overrides);
}

test('store creates an owned car with features and two images and returns 201', function () {
    Storage::fake('public');

    $catalog = apiCarCatalog();

    Sanctum::actingAs($catalog['user']);

    $response = $this->post('/api/v1/cars', apiCarPayload($catalog, [
        'published' => 1,
        'abs' => 1,
        'images' => [
            UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
            UploadedFile::fake()->create('b.jpg', 100, 'image/jpeg'),
        ],
    ]));

    $response->assertCreated()
        ->assertJsonPath('data.owner.id', $catalog['user']->id)
        ->assertJsonPath('data.features.abs', true);

    $car = Car::where('user_id', $catalog['user']->id)->firstOrFail();

    expect($car->features->abs)->toBeTrue();
    expect($car->images)->toHaveCount(2);
    expect($car->images->pluck('position')->all())->toBe([1, 2]);
    expect(Storage::disk('public')->allFiles('cars/'.$car->id))->toHaveCount(2);
});

test('store with empty data returns 422 JSON', function () {
    $catalog = apiCarCatalog();

    Sanctum::actingAs($catalog['user']);

    $response = $this->post('/api/v1/cars', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['maker_id', 'model_id', 'vin']);
});

test('an unauthenticated post returns 401 JSON', function () {
    $response = $this->post('/api/v1/cars', []);

    $response->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
});

test('update by the owner changes the price', function () {
    Storage::fake('public');

    $catalog = apiCarCatalog();

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

    Sanctum::actingAs($catalog['user']);

    $this->put('/api/v1/cars/'.$car->id, apiCarPayload($catalog, [
        'vin' => $car->vin,
        'price' => 900000,
        'published' => 1,
    ]))
        ->assertOk()
        ->assertJsonPath('data.price', 900000);

    $car->refresh();

    expect($car->price)->toBe(900000);
    expect($car->published_at)->not->toBeNull();
});

test('update by another user is forbidden', function () {
    Storage::fake('public');

    $catalog = apiCarCatalog();

    $car = Car::factory()->create([
        'user_id' => $catalog['user']->id,
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
    ]);

    Sanctum::actingAs(User::factory()->create());

    $this->put('/api/v1/cars/'.$car->id, apiCarPayload($catalog, ['vin' => $car->vin]))
        ->assertForbidden();
});

test('destroy by the owner returns 204 and soft deletes the car', function () {
    $catalog = apiCarCatalog();

    $car = Car::factory()->create(['user_id' => $catalog['user']->id]);

    Sanctum::actingAs($catalog['user']);

    $this->delete('/api/v1/cars/'.$car->id)->assertNoContent();

    $this->assertSoftDeleted('cars', ['id' => $car->id]);
});

test('destroy by another user is forbidden', function () {
    $catalog = apiCarCatalog();

    $car = Car::factory()->create(['user_id' => $catalog['user']->id]);

    Sanctum::actingAs(User::factory()->create());

    $this->delete('/api/v1/cars/'.$car->id)->assertForbidden();

    $this->assertDatabaseHas('cars', ['id' => $car->id, 'deleted_at' => null]);
});

test('restore by the owner returns 200 and clears the soft delete', function () {
    $catalog = apiCarCatalog();

    $car = Car::factory()->create(['user_id' => $catalog['user']->id]);
    $car->delete();

    Sanctum::actingAs($catalog['user']);

    $this->patch('/api/v1/cars/'.$car->id.'/restore')
        ->assertOk()
        ->assertJsonPath('data.id', $car->id);

    $this->assertNotSoftDeleted('cars', ['id' => $car->id]);
});

test('restore by another user is forbidden', function () {
    $catalog = apiCarCatalog();

    $car = Car::factory()->create(['user_id' => $catalog['user']->id]);
    $car->delete();

    Sanctum::actingAs(User::factory()->create());

    $this->patch('/api/v1/cars/'.$car->id.'/restore')->assertForbidden();

    $this->assertSoftDeleted('cars', ['id' => $car->id]);
});
