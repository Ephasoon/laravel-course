<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('login returns a token and user', function () {
    $user = User::factory()->create([
        'email' => 'driver@example.com',
        'password' => 'secret-password',
    ]);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'driver@example.com',
        'password' => 'secret-password',
        'device_name' => 'pest',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token', 'user' => ['id', 'name']]);

    expect($response->json('token'))->toBeString()->not->toBeEmpty();
    expect($response->json('user.id'))->toBe($user->id);

    $this->assertDatabaseCount('personal_access_tokens', 1);
});

test('a wrong password returns 422', function () {
    User::factory()->create(['email' => 'driver@example.com']);

    $response = $this->postJson('/api/v1/login', [
        'email' => 'driver@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('email');

    expect($response->json('errors.email.0'))->toBe('These credentials do not match our records.');
    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('logout removes the current access token', function () {
    $user = User::factory()->create();
    $token = $user->createToken('pest')->plainTextToken;

    $this->assertDatabaseCount('personal_access_tokens', 1);

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson('/api/v1/logout')
        ->assertNoContent();

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test('the user endpoint requires a token and returns the profile with one', function () {
    $this->getJson('/api/v1/user')->assertUnauthorized();

    $user = User::factory()->create(['phone' => '0912345678']);

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/v1/user');

    $response->assertOk()
        ->assertJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '0912345678',
        ]);
});
