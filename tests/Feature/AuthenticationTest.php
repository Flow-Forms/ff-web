<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('failed login shows error message', function () {
    $user = User::factory()->create();

    // Simulate real browser flow: visit login page first, then submit with wrong password
    // The from() helper sets the previous URL so redirect()->back() works correctly
    $response = $this->from('/login')->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrors('email');
    $response->assertRedirect('/login');

    // Follow the redirect and verify error message is displayed
    $this->followRedirects($response)->assertSee(__('auth.failed'));
});
