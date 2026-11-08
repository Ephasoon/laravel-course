<?php

use App\Models\Car;
use App\Models\CarImages;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('favourite toggles on then off', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['published_at' => now()]);

    Sanctum::actingAs($user);

    $this->post('/api/v1/cars/'.$car->id.'/favourite')
        ->assertOk()
        ->assertJson(['favourited' => true]);

    $this->assertDatabaseHas('favourite_cars', [
        'user_id' => $user->id,
        'car_id' => $car->id,
    ]);

    $this->post('/api/v1/cars/'.$car->id.'/favourite')
        ->assertOk()
        ->assertJson(['favourited' => false]);

    $this->assertDatabaseMissing('favourite_cars', [
        'user_id' => $user->id,
        'car_id' => $car->id,
    ]);
});

test('an unpublished car from another user is not found', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['published_at' => null]);

    Sanctum::actingAs($user);

    $this->post('/api/v1/cars/'.$car->id.'/favourite')->assertNotFound();
});

test('favourites shows only the authenticated user favourites', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $mine = Car::factory()->count(2)->create(['published_at' => now()]);
    $theirs = Car::factory()->create(['published_at' => now()]);

    $user->favouriteCars()->attach($mine->pluck('id')->all());
    $other->favouriteCars()->attach($theirs->id);

    Sanctum::actingAs($user);

    $response = $this->get('/api/v1/favourites');

    $response->assertOk();

    $ids = collect($response->json('data'))->pluck('id')->sort()->values()->all();

    expect($ids)->toBe($mine->pluck('id')->sort()->values()->all());
});

test('a guest cannot list favourites', function () {
    $this->get('/api/v1/favourites')->assertUnauthorized();
});

test('public cars json hides vin and address', function () {
    Car::factory()->create(['published_at' => now()]);

    $response = $this->get('/api/v1/cars');

    $response->assertOk()
        ->assertJsonMissingPath('data.0.vin')
        ->assertJsonMissingPath('data.0.address');

    expect($response->json('data.0'))->not->toHaveKey('vin');
    expect($response->json('data.0'))->not->toHaveKey('address');
});

test('the owner my cars response contains vin and address', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create([
        'user_id' => $user->id,
        'published_at' => now(),
        'address' => 'Bole',
    ]);

    Sanctum::actingAs($user);

    $this->get('/api/v1/my-cars')
        ->assertOk()
        ->assertJsonPath('data.0.vin', $car->vin)
        ->assertJsonPath('data.0.address', 'Bole');
});

test('image urls are absolute', function () {
    $car = Car::factory()->create(['published_at' => now()]);

    CarImages::factory()->create([
        'car_id' => $car->id,
        'image_path' => '/img/cars/Lexus-RX200t-2016/1.jpeg',
        'position' => 1,
    ]);

    $response = $this->get('/api/v1/cars/'.$car->id);

    $response->assertOk();

    expect($response->json('data.primary_image'))->toStartWith('http');
    expect($response->json('data.images.0.url'))->toStartWith('http');
});
