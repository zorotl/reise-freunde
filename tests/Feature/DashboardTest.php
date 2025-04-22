<?php

use App\Models\User;

// test('guests are redirected to the login page', function () {
//     $response = $this->get('/dashboard');
//     $response->assertRedirect('/login');
// });

// test('authenticated users can visit the dashboard', function () {
//     $user = User::factory()->create();
//     $this->actingAs($user);

//     $response = $this->get('/dashboard');
//     $response->assertStatus(200);
// });

test('users are redirected to the post overview', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/post/show');
});
