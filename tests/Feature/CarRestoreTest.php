<?php

use App\Models\Car;
use App\Models\User;

test('the owner restores a deleted car and it shows on the home page again', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $user->id, 'published_at' => now()]);
    $car->delete();

    $this->get('/')->assertDontSee($car->maker->name.' '.$car->model->name);

    $response = $this->actingAs($user)->patch(route('car.restore', $car));

    $response->assertRedirect(route('car.index'));
    $response->assertSessionHas('success', 'Car restored successfully.');
    $this->assertNotSoftDeleted('cars', ['id' => $car->id]);

    $this->get('/')->assertSee($car->maker->name.' '.$car->model->name);
});

test('another user gets 403 when restoring a deleted car', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id, 'published_at' => now()]);
    $car->delete();

    $this->actingAs($other)
        ->patch(route('car.restore', $car))
        ->assertForbidden();

    $this->assertSoftDeleted('cars', ['id' => $car->id]);
});

test('a guest is redirected to login when restoring a car', function () {
    $owner = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id, 'published_at' => now()]);
    $car->delete();

    $this->patch(route('car.restore', $car))
        ->assertRedirect(route('login'));
});

test('the index shows a restore form only for trashed cars', function () {
    $user = User::factory()->create();
    $active = Car::factory()->create(['user_id' => $user->id, 'published_at' => now()]);
    $trashed = Car::factory()->create(['user_id' => $user->id, 'published_at' => now()]);
    $trashed->delete();

    $response = $this->actingAs($user)->get(route('car.index'));

    $response->assertSee('action="'.route('car.restore', $trashed).'"', false);
    $response->assertDontSee('action="'.route('car.destroy', $trashed).'"', false);
    $response->assertSee('action="'.route('car.destroy', $active).'"', false);
    $response->assertDontSee('action="'.route('car.restore', $active).'"', false);
});

test('the index page has balanced form and svg markup', function () {
    $user = User::factory()->create();
    Car::factory()->create(['user_id' => $user->id, 'published_at' => now()]);
    $trashed = Car::factory()->create(['user_id' => $user->id, 'published_at' => now()]);
    $trashed->delete();

    $html = $this->actingAs($user)->get(route('car.index'))->getContent();

    expect(substr_count($html, '<form'))->toBe(substr_count($html, '</form>'));
    expect(substr_count($html, '<svg'))->toBe(substr_count($html, '</svg>'));
});
