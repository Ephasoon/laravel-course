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
        ->assertSee($car->maker->name.' '.$car->model->name);

    $this->actingAs($otherUser)
        ->get(route('watchlist'))
        ->assertDontSee($car->maker->name.' '.$car->model->name);
});

test('the home page marks a favourited car with is-favourite', function () {
    $user = User::factory()->create();
    $car = Car::factory()->create(['published_at' => now()]);

    $this->actingAs($user)
        ->post(route('favourite.toggle', $car));

    $response = $this->get('/');

    $response->assertSee('btn-heart is-favourite');
});

test('the home page has balanced form and svg markup for guests and users', function () {
    Car::factory()->count(3)->create(['published_at' => now()]);

    $guestHtml = $this->get('/')->getContent();

    expect(substr_count($guestHtml, '<form'))->toBe(substr_count($guestHtml, '</form>'));
    expect(substr_count($guestHtml, '<svg'))->toBe(substr_count($guestHtml, '</svg>'));

    $this->actingAs(User::factory()->create());

    $userHtml = $this->get('/')->getContent();

    expect(substr_count($userHtml, '<form'))->toBe(substr_count($userHtml, '</form>'));
    expect(substr_count($userHtml, '<svg'))->toBe(substr_count($userHtml, '</svg>'));
});
