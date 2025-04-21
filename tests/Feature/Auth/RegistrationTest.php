<?php

use Livewire\Volt\Volt;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = Volt::test('auth.register')
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('username', 'user1234')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('registration fails with duplicate username', function () {
    $existingUser = \App\Models\User::factory()->create();
    $existingUser->additionalInfo()->create([
        'username' => 'username123',
    ]);

    $response = Volt::test('auth.register')
        ->set('name', 'Neuer Nutzer')
        ->set('email', 'neu@example.com')
        ->set('username', 'username123') // gleicher Username
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register');

    $response->assertHasErrors(['username']);
});