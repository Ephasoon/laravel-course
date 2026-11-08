<?php

use App\Models\Car;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('cars index returns paginated published cars only', function () {
    $published = Car::factory()->create(['published_at' => now()]);
    Car::factory()->create(['published_at' => null]);

    $response = $this->getJson('/api/v1/cars');

    $response->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($published->id);
});

test('cars index filters by maker', function () {
    $maker = Maker::factory()->create();
    $model = Model::factory()->create(['maker_id' => $maker->id]);
    $matching = Car::factory()->create([
        'maker_id' => $maker->id,
        'model_id' => $model->id,
        'published_at' => now(),
    ]);
    Car::factory()->create(['published_at' => now()]);

    $response = $this->getJson('/api/v1/cars?maker_id='.$maker->id);

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($matching->id);
});

test('an unpublished car returns 404', function () {
    $car = Car::factory()->create(['published_at' => null]);

    $this->getJson('/api/v1/cars/'.$car->id)->assertNotFound();
});

test('a car response never exposes private owner fields', function () {
    $car = Car::factory()->create(['published_at' => now()]);

    $response = $this->getJson('/api/v1/cars/'.$car->id);

    $response->assertOk()
        ->assertJsonMissingPath('owner.email')
        ->assertJsonMissingPath('owner.password')
        ->assertJsonMissingPath('owner.remember_token');

    expect($response->json('owner'))->not->toHaveKey('email');
    expect($response->json('owner'))->not->toHaveKey('password');
    expect($response->json('owner'))->not->toHaveKey('remember_token');

    expect($response->getContent())
        ->not->toContain('"email"')
        ->not->toContain('"password"')
        ->not->toContain('"remember_token"');
});

test('my cars returns only the authenticated user cars including unpublished', function () {
    $this->getJson('/api/v1/my-cars')->assertUnauthorized();

    $user = User::factory()->create();
    $mine = Car::factory()->create(['user_id' => $user->id, 'published_at' => null]);
    Car::factory()->create(['published_at' => now()]);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/my-cars');

    $response->assertOk();

    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($mine->id);
});
