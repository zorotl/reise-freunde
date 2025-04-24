<?php

use App\Models\User;
use App\Models\UserGrant;
use Livewire\Livewire;
use App\Livewire\Admin\Users\ManageUsers;
use App\Livewire\Admin\Users\EditUserModal;

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

test('non admin/moderator users cannot access admin user management', function () {
    $user = User::factory()->create();
    UserGrant::where('user_id', $user->id)->delete(); // Ensure no admin/moderator grant

    actingAs($user)
        ->get(route('admin.users'))
        ->assertForbidden(); // Or the redirect you chose in middleware
});

test('admin users can access admin user management', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.users'))
        ->assertOk();
});

test('moderator users can access admin user management', function () {
    $moderatorUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorUser->id], ['is_moderator' => true]);

    actingAs($moderatorUser)
        ->get(route('admin.users'))
        ->assertOk();
});

test('guest users are redirected to login when trying to access admin user management', function () {
    get(route('admin.users'))
        ->assertRedirect(route('login'));
});

