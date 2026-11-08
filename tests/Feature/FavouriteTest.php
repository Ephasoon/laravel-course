<?php

use App\Models\Car;
use App\Models\User;

test('a guest is redirected to login on toggle', function () {
    $car = Car::factory()->create(['published_at' => now()]);

    $this->post(route('favourite.toggle', $car))
        ->assertRedirect(route('login'));
});

test('toggle adds a favourite then removes it', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['published_at' => now()]);

    $this->actingAs($user)
        ->post(route('favourite.toggle', $car))
        ->assertRedirect();

    $user->refresh();

    expect($user->favouriteCars->contains($car->id))->toBeTrue();

    $this->actingAs($user)
        ->post(route('favourite.toggle', $car));

    $user->refresh();

    expect($user->favouriteCars->contains($car->id))->toBeFalse();
});

test('a user cannot favourite another user\'s unpublished car (404)', function () {
    $owner = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id, 'published_at' => null]);

    $this->actingAs(User::factory()->create())
        ->post(route('favourite.toggle', $car))
        ->assertNotFound();
});

test('the watchlist shows only the user\'s own favourites', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $car = Car::factory()->create(['published_at' => now()]);

    $this->actingAs($user)
        ->post(route('favourite.toggle', $car));

    $this->actingAs($user)
        ->get(route('watchlist'))
        ->assertSee($car->maker->name . ' ' . $car->model->name);

    $this->actingAs($otherUser)
        ->get(route('watchlist'))
        ->assertDontSee($car->maker->name . ' ' . $car->model->name);
});

test('the home page marks a favourited car with is-favourite', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['published_at' => now()]);

    $this->actingAs($user)
        ->post(route('favourite.toggle', $car));

    $response = $this->get('/');

    $response->assertSee('btn-heart is-favourite');
});

test('markup test: on the home page, substr_count of "<form" equals substr_count of "</form>"', function () {
    $response = $this->get('/');
    $html = $response->getContent();

    assert(substr_count($html, '<form') === substr_count($html, '</form>'));
});

test('markup test: on the home page, substr_count of "<svg" equals "</svg>"', function () {
    $response = $this->get('/');
    $html = $response->getContent();

    assert(substr_count($html, '<svg') === substr_count($html, '</svg>'));
});