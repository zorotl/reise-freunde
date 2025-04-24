<?php

use App\Models\User;
use App\Models\UserGrant;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('non admin/moderator users cannot access admin dashboard', function () {
    // Create a regular user
    $user = User::factory()->create();

    // Ensure they do NOT have admin or moderator grants
    UserGrant::where('user_id', $user->id)->delete(); // Remove any default grant if factory creates it


    // Act as the regular user and try to access the admin dashboard route
    actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden(); // Expect a 403 Forbidden response
});

test('admin users can access admin dashboard', function () {
    // Create an admin user
    $adminUser = User::factory()->create();
    // Ensure they have admin grant
    UserGrant::updateOrCreate(
        ['user_id' => $adminUser->id],
        ['is_admin' => true, 'is_moderator' => false]
    );

    // Act as the admin user and try to access the admin dashboard route
    actingAs($adminUser)
        ->get(route('admin.dashboard'))
        ->assertOk(); // Expect a successful response (status 200)
});

test('moderator users can access admin dashboard', function () {
    // Create a moderator user
    $moderatorUser = User::factory()->create();
    // Ensure they have moderator grant
    UserGrant::updateOrCreate(
        ['user_id' => $moderatorUser->id],
        ['is_admin' => false, 'is_moderator' => true]
    );

    // Act as the moderator user and try to access the admin dashboard route
    actingAs($moderatorUser)
        ->get(route('admin.dashboard'))
        ->assertOk(); // Expect a successful response (status 200)
});

test('guest users are redirected to login when trying to access admin dashboard', function () {
    // Access the admin dashboard route without being authenticated
    get(route('admin.dashboard'))
        ->assertRedirect(route('login')); // Expect a redirect to the login page
});

// You might need to adjust the UserFactory to ensure no UserGrant is created by default,
// or add a step to delete it in the tests for regular users.
// Alternatively, update your UserFactory to optionally create a UserGrant.