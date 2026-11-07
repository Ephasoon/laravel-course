<?php

use App\Models\Car;
use App\Models\CarFeatures;
use App\Models\CarImages;
use App\Models\User;

test('guests are redirected from car index to login', function () {
    $this->get(route('car.index'))->assertRedirect(route('login'));
});

test('car index shows only the authenticated users cars', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownCar = Car::factory()->create(['user_id' => $owner->id]);
    Car::factory()->create(['user_id' => $other->id]);

    $response = $this->actingAs($owner)->get(route('car.index'));

    $response->assertOk();

    expect($response->viewData('cars')->pluck('id')->all())->toBe([$ownCar->id]);
});

test('car show displays the real car details and ordered relations', function () {
    $car = Car::factory()->create([
        'price' => 1234567,
        'mileage' => 54321,
        'phone' => '0911223344',
        'description' => 'A very clean car.',
        'published_at' => now(),
    ]);

    CarFeatures::factory()->create([
        'car_id' => $car->id,
        'abs' => true,
        'leather_seats' => false,
    ]);

    $secondImage = CarImages::factory()->create([
        'car_id' => $car->id,
        'image_path' => '/img/cars/Lexus-RX200t-2016/2.jpeg',
        'position' => 2,
    ]);
    $firstImage = CarImages::factory()->create([
        'car_id' => $car->id,
        'image_path' => '/img/cars/Lexus-RX200t-2016/1.jpeg',
        'position' => 1,
    ]);

    $response = $this->get(route('car.show', $car));

    $response->assertOk()
        ->assertSee($car->maker->name)
        ->assertSee($car->model->name)
        ->assertSee(number_format($car->price))
        ->assertSee($car->city->name)
        ->assertSee($car->user->name)
        ->assertSee('A very clean car.')
        ->assertSee('ABS')
        ->assertSee($firstImage->image_path)
        ->assertSee($secondImage->image_path)
        ->assertSee($car->phone);

    expect($response->viewData('car')->images->pluck('position')->all())->toBe([1, 2])
        ->and($response->viewData('car')->features->abs)->toBeTrue();
});
