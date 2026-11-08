<?php

use App\Models\Car;
use App\Models\CarType;
use App\Models\City;
use App\Models\FuelType;
use App\Models\Maker;
use App\Models\Model;
use App\Models\User;
use App\Notifications\CarPublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

function notificationCatalog(): array
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

function notificationPayload(array $catalog, array $overrides = []): array
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

test('creating a published car notifies the owner once', function () {
    Notification::fake();
    Storage::fake('public');

    $catalog = notificationCatalog();

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), notificationPayload($catalog, ['published' => 1]))
        ->assertRedirect(route('car.index'));

    Notification::assertSentToTimes($catalog['user'], CarPublishedNotification::class, 1);
});

test('creating an unpublished car sends nothing', function () {
    Notification::fake();
    Storage::fake('public');

    $catalog = notificationCatalog();

    $this->actingAs($catalog['user'])
        ->post(route('car.store'), notificationPayload($catalog))
        ->assertRedirect(route('car.index'));

    Notification::assertNothingSentTo($catalog['user']);
});

test('updating an unpublished car to published notifies once', function () {
    Notification::fake();
    Storage::fake('public');

    $catalog = notificationCatalog();

    $car = Car::factory()->create([
        'user_id' => $catalog['user']->id,
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
        'published_at' => null,
    ]);

    $this->actingAs($catalog['user'])
        ->put(route('car.update', $car), notificationPayload($catalog, [
            'vin' => $car->vin,
            'published' => 1,
        ]))
        ->assertRedirect(route('car.index'));

    Notification::assertSentToTimes($catalog['user'], CarPublishedNotification::class, 1);
});

test('updating an already published car sends nothing', function () {
    Notification::fake();
    Storage::fake('public');

    $catalog = notificationCatalog();

    $car = Car::factory()->create([
        'user_id' => $catalog['user']->id,
        'maker_id' => $catalog['maker']->id,
        'model_id' => $catalog['model']->id,
        'car_type_id' => $catalog['car_type']->id,
        'fuel_type_id' => $catalog['fuel_type']->id,
        'city_id' => $catalog['city']->id,
        'published_at' => now(),
    ]);

    $this->actingAs($catalog['user'])
        ->put(route('car.update', $car), notificationPayload($catalog, [
            'vin' => $car->vin,
            'published' => 1,
        ]))
        ->assertRedirect(route('car.index'));

    Notification::assertNothingSentTo($catalog['user']);
});

test('the notification is queued', function () {
    $notification = new CarPublishedNotification(Car::factory()->make());

    expect(CarPublishedNotification::class)->toImplement(ShouldQueue::class);
    expect($notification->via(User::factory()->make()))->toBe(['mail']);
});

test('the mail content mentions the car maker and links to the car', function () {
    $car = Car::factory()->create();
    $car->load('maker', 'model');

    $notification = new CarPublishedNotification($car);
    $mail = $notification->toMail($car->user);

    expect($mail->subject)->toBe('Your car is now published');
    expect($mail->actionUrl)->toBe(route('car.show', $car));
    expect((string) $mail->render())->toContain($car->maker->name);
});
