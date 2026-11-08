<?php

use App\Models\Car;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;

test('owner can delete a car (soft delete)', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create([
        'user_id' => $user->id,
        'published_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->delete(route('car.destroy', $car));

    $response->assertRedirect(route('car.index'));
    $this->assertSoftDeleted('cars', ['id' => $car->id]);
    $this->assertDatabaseHas('cars', ['id' => $car->id]);
});

test('another user gets 403 on edit', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->get(route('car.edit', $car))
        ->assertForbidden();
});

test('another user gets 403 on update', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->put(route('car.update', $car), [
            'price' => 999,
        ])
        ->assertStatus(403);
});

test('another user gets 403 on destroy', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id]);

    $response = $this->actingAs($other)
        ->delete(route('car.destroy', $car));

    $response->assertStatus(403);
});

test('a guest is redirected to login on destroy', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $user->id]);

    $response = $this->delete(route('car.destroy', $car));

    $response->assertRedirect(route('login'));
});

test('deleted cars disappear from home', function () {
    $user = User::factory()->create();
    $maker = Maker::factory()->create();
    $model = Model::factory()->create(['maker_id' => $maker->id]);
    $car = Car::factory()->create([
        'user_id' => $user->id,
        'published_at' => now(),
        'maker_id' => $maker->id,
        'model_id' => $model->id,
    ]);

    $this->actingAs($user)
        ->delete(route('car.destroy', $car));

    $this->get('/')
        ->assertDontSee($car->maker->name.' '.$car->model->name);
});

test('deleted cars disappear from search', function () {
    $user = User::factory()->create();
    $maker = Maker::factory()->create();
    $model = Model::factory()->create(['maker_id' => $maker->id]);
    $car = Car::factory()->create([
        'user_id' => $user->id,
        'published_at' => now(),
        'maker_id' => $maker->id,
        'model_id' => $model->id,
    ]);

    $this->actingAs($user)
        ->delete(route('car.destroy', $car));

    $this->get(route('car.search'))
        ->assertDontSee($car->maker->name.' '.$car->model->name);
});

test('deleted cars show 404', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create([
        'user_id' => $user->id,
        'published_at' => now(),
    ]);

    $this->actingAs($user)
        ->delete(route('car.destroy', $car));

    $this->get(route('car.show', $car))
        ->assertNotFound();
});
