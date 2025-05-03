<?php

use App\Models\User;
use App\Models\Post;
use App\Models\UserGrant;
use Livewire\Livewire;

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

test('admin can access the edit post page', function () {
    $adminUser = User::factory()->create();
    UserGrant::factory()->create(['user_id' => $adminUser->id, 'is_admin' => true]);
    $post = Post::factory()->create(); // Create a post to edit

    actingAs($adminUser)
        ->get(route('admin.posts.edit', $post)) // Access the edit route
        ->assertOk()
        ->assertSee('Edit Post'); // Check for text unique to the edit page
    // ->assertSeeLivewire(\App\Livewire\Admin\Posts\EditPost::class); // Optional: Assert the component is loaded
});

test('admin can update post details', function () {
    $adminUser = User::factory()->create();
    UserGrant::factory()->create(['user_id' => $adminUser->id, 'is_admin' => true]);
    $post = Post::factory()->create([
        'title' => 'Old Title',
        'content' => 'Old Content',
    ]);

    $newTitle = 'Updated Post Title';
    $newContent = 'Updated post content.';

    actingAs($adminUser); // Act as admin for the Livewire test

    Livewire::test(\App\Livewire\Admin\Posts\EditPost::class, ['post' => $post]) // Test the full-page component
        ->set('title', $newTitle)
        ->set('content', $newContent)
        ->set('is_active', true)
        ->set('expiryDate', now()->addDays(10)->format('Y-m-d'))
        ->set('fromDate', now()->format('Y-m-d'))
        ->set('toDate', now()->addDays(5)->format('Y-m-d'))
        ->call('update')
        ->assertRedirect(route('admin.posts')); // Assert redirection after successful save

    // Verify the changes in the database
    $post->refresh();
    expect($post->title)->toBe($newTitle);
    expect($post->content)->toBe($newContent);
});

test('edit post validation works', function () {
    $adminUser = User::factory()->create();
    UserGrant::factory()->create(['user_id' => $adminUser->id, 'is_admin' => true]);
    $post = Post::factory()->create();

    actingAs($adminUser);

    Livewire::test(\App\Livewire\Admin\Posts\EditPost::class, ['postId' => $post->id])
        ->set('title', '') // Set title to empty to trigger validation
        ->set('content', '') // Set content to empty
        ->call('update')
        ->assertHasErrors(['title' => 'required']) // Check for specific validation errors
        ->assertHasErrors(['content' => 'required']);

    // Add more checks for other validation rules (min length, etc.)
    Livewire::test(\App\Livewire\Admin\Posts\EditPost::class, ['postId' => $post->id])
        ->set('title', 'ab') // Too short title
        ->call('update')
        ->assertHasErrors(['title' => 'min']); // Assuming a min rule exists
});