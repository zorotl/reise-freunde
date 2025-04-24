<?php

use App\Models\User;
use App\Models\Post;
use App\Models\UserGrant;
use Livewire\Livewire;
use App\Livewire\Admin\Posts\EditPostModal;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('non admin/moderator users cannot access admin post management', function () {
    $user = User::factory()->create();
    UserGrant::where('user_id', $user->id)->delete(); // Ensure no admin/moderator grant

    actingAs($user)
        ->get(route('admin.posts'))
        ->assertForbidden(); // Or the redirect you chose in middleware
});

test('admin users can access admin post management', function () {
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    actingAs($adminUser)
        ->get(route('admin.posts'))
        ->assertOk();
});

test('moderator users can access admin post management', function () {
    $moderatorUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $moderatorUser->id], ['is_moderator' => true]);

    actingAs($moderatorUser)
        ->get(route('admin.posts'))
        ->assertOk();
});

test('guest users are redirected to login when trying to access admin post management', function () {
    get(route('admin.posts'))
        ->assertRedirect(route('login'));
});

test('admin can update post details', function () {
    // Arrange: Create an admin user and a target post
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);

    $author = User::factory()->create(); // Post needs an author
    $targetPost = Post::factory()->create([
        'user_id' => $author->id,
        'title' => 'Original Title',
        'content' => 'Original content.',
        'expiry_date' => now()->addDays(10),
        'is_active' => true,
        'from_date' => now()->subDays(5),
        'to_date' => now()->addDays(5),
        'country' => 'Old Country',
        'city' => 'Old City',
    ]);

    // Act: Act as the admin user and interact with the modal component
    actingAs($adminUser);

    Livewire::test(EditPostModal::class)
        // Call the method to open the modal and load the target post data
        ->call('openEditPostModal', $targetPost->id)
        // Assert that properties are loaded (checking formatted dates)
        ->assertSet('postId', $targetPost->id)
        ->assertSet('title', $targetPost->title)
        ->assertSet('content', $targetPost->content)
        ->assertSet('expiry_date', $targetPost->expiry_date->format('Y-m-d'))
        ->assertSet('is_active', $targetPost->is_active)
        ->assertSet('from_date', $targetPost->from_date->format('Y-m-d'))
        ->assertSet('to_date', $targetPost->to_date->format('Y-m-d'))
        ->assertSet('country', $targetPost->country)
        ->assertSet('city', $targetPost->city)
        ->assertSet('show', true) // Assert modal is open

        // Modify the properties like a user would interact with the form
        ->set('title', 'Updated Title')
        ->set('content', 'Updated content with more details.')
        ->set('expiry_date', now()->addDays(20)->format('Y-m-d'))
        ->set('is_active', false) // Set to inactive
        ->set('from_date', now()->subDays(2)->format('Y-m-d'))
        ->set('to_date', now()->addDays(15)->format('Y-m-d'))
        ->set('country', 'New Country')
        ->set('city', 'New City')

        // Call the save method
        ->call('savePost')
        // Assert validation passes and modal closes
        ->assertHasNoErrors()
        ->assertSet('show', false)
        // Assert an event is dispatched to refresh the list
        ->assertDispatched('postUpdated');

    // Assert: Verify the database was updated correctly
    $targetPost->refresh(); // Refresh the post model from DB

    expect($targetPost->title)->toBe('Updated Title');
    expect($targetPost->content)->toBe('Updated content with more details.');
    expect($targetPost->expiry_date->startOfDay()->equalTo(now()->addDays(20)->startOfDay()))->toBeTrue();
    expect($targetPost->is_active)->toBeFalse();
    expect($targetPost->from_date->startOfDay()->equalTo(now()->subDays(2)->startOfDay()))->toBeTrue();
    expect($targetPost->to_date->startOfDay()->equalTo(now()->addDays(15)->startOfDay()))->toBeTrue();
    expect($targetPost->country)->toBe('New Country');
    expect($targetPost->city)->toBe('New City');
});

test('edit post validation works', function () {
    // Arrange: Create an admin user and a target post
    $adminUser = User::factory()->create();
    UserGrant::updateOrCreate(['user_id' => $adminUser->id], ['is_admin' => true]);
    $author = User::factory()->create();
    $targetPost = Post::factory()->create(['user_id' => $author->id]);

    // Act: Act as the admin user and interact with the modal component
    actingAs($adminUser);

    Livewire::test(EditPostModal::class)
        ->call('openEditPostModal', $targetPost->id)
        // Attempt to save with invalid data
        ->set('title', '') // Invalid: required
        ->set('content', '') // Invalid: required
        ->set('expiry_date', 'invalid-date') // Invalid date
        ->set('from_date', now()->addDays(10)->format('Y-m-d'))
        ->set('to_date', now()->addDays(5)->format('Y-m-d')) // Invalid: to_date before from_date
        ->set('is_active', null) // Invalid: boolean

        ->call('savePost')
        // Assert validation errors
        ->assertHasErrors(['title', 'content', 'expiry_date', 'to_date', 'is_active'])
        // Assert modal stays open on validation errors
        ->assertSet('show', true);
});