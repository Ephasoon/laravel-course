<?php

use App\Models\User;

test('signup creates a user and logs them in', function () {
    $response = $this->post(route('signup'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '0912345678',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHas('success');

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => '0912345678',
    ]);
});

test('duplicate email is rejected', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $this->post(route('signup'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('password confirmation mismatch is rejected', function () {
    $this->post(route('signup'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ])->assertSessionHasErrors('password');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
});

test('login with the seeded user works', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user);
});

test('a wrong password is rejected', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->from(route('login'))
        ->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('logout works', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect('/');

    $this->assertGuest();
});

test('a logged in user visiting login is redirected away', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect('/');
});

test('a guest is sent to login and returned to the create form after login', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $this->get(route('car.create'))->assertRedirect(route('login'));

    $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ])->assertRedirect(route('car.create'));
});
