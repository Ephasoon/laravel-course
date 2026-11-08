<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

test('the forgot password page loads', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertSee('Forgot Password')
        ->assertSee('action="'.route('password.email').'"', false);
});

test('submitting a real email sends a reset link', function () {
    Notification::fake();

    $user = User::factory()->create(['email' => 'jane@example.com']);

    $this->post(route('password.email'), ['email' => 'jane@example.com'])
        ->assertSessionHas('success', 'If that email exists, we sent a reset link.');

    Notification::assertSentToTimes($user, ResetPassword::class, 1);
});

test('submitting an unknown email sends nothing but shows the same message', function () {
    Notification::fake();

    $this->post(route('password.email'), ['email' => 'nobody@example.com'])
        ->assertSessionHas('success', 'If that email exists, we sent a reset link.');

    Notification::assertNothingSent();
});

test('the reset password page loads with a token', function () {
    $this->get(route('password.reset', ['token' => 'test-token', 'email' => 'jane@example.com']))
        ->assertOk()
        ->assertSee('Reset Password')
        ->assertSee('value="test-token"', false)
        ->assertSee('value="jane@example.com"', false);
});

test('a valid token resets the password and the new password can log in', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $token = Password::broker()->createToken($user);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => 'jane@example.com',
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('success', 'Your password has been reset.');

    expect(Hash::check('new-password123', $user->fresh()->password))->toBeTrue();

    $this->post(route('login'), [
        'email' => 'jane@example.com',
        'password' => 'new-password123',
    ])->assertRedirect('/');

    $this->assertAuthenticatedAs($user->fresh());
});

test('an invalid token fails and leaves the password unchanged', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $this->from(route('password.reset', ['token' => 'not-a-real-token']))
        ->post(route('password.update'), [
            'token' => 'not-a-real-token',
            'email' => 'jane@example.com',
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ])
        ->assertRedirect(route('password.reset', ['token' => 'not-a-real-token']))
        ->assertSessionHasErrors('email');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('a password confirmation mismatch fails', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => 'password',
    ]);

    $token = Password::broker()->createToken($user);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => 'jane@example.com',
        'password' => 'new-password123',
        'password_confirmation' => 'different-password',
    ])->assertSessionHasErrors('password');

    expect(Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('a logged in user is redirected away from the forgot password page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('password.request'))
        ->assertRedirect('/');
});

test('the login page links to the password request route', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee(route('password.request'), false);
});
