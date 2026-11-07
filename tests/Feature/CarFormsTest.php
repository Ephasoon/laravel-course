<?php

use App\Models\Car;
use App\Models\Maker;
use App\Models\User;

test('guests are redirected to login from the create form', function () {
    $this->get(route('car.create'))->assertRedirect(route('login'));
});

test('a logged in user sees the create form with maker options', function () {
    $user = User::factory()->create();
    Maker::factory()->create(['name' => 'Toyota']);

    $this->actingAs($user)
        ->get(route('car.create'))
        ->assertOk()
        ->assertSee('name="maker_id"', false)
        ->assertSee('Toyota');
});

test('the edit form is prefilled with the car year', function () {
    $owner = User::factory()->create();
    $car = Car::factory()->create([
        'user_id' => $owner->id,
        'year' => 2017,
    ]);

    $this->actingAs($owner)
        ->get(route('car.edit', $car))
        ->assertOk()
        ->assertSee('value="2017" selected', false);
});

test('a non owner gets 403 on the edit form', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $car = Car::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($other)
        ->get(route('car.edit', $car))
        ->assertForbidden();
});
