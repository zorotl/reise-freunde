<?php
// tests/Feature/PostLikeTest.php

use App\Models\User;
use App\Models\Post;
use App\Livewire\Parts\PostCardSection; // Import the component being tested
use Livewire\Livewire; // Import Livewire testing helper

use function Pest\Laravel\actingAs; // Helper for acting as a user
// use function Pest\Laravel\get; // Not needed for these specific tests

// Test that an authenticated user can like a post they haven't liked yet
test('authenticated user can like a post', function () {
    // Arrange: Create a user to act as, and a post (by default, created by another user via factory)
    $user = User::factory()->create();
    $post = Post::factory()->create(); // Post to be liked

    // Act & Assert: Use Livewire test helper
    actingAs($user); // Simulate the user being logged in

    Livewire::test(PostCardSection::class, ['post' => $post, 'show' => 'feed']) // Mount the component with necessary props
        ->assertSet('isLiked', false) // Assert initial state is not liked
        ->assertSet('likesCount', 0) // Assert initial count is 0
        ->call('toggleLike') // Call the like action
        ->assertSet('isLiked', true) // Assert state updated to liked
        ->assertSet('likesCount', 1); // Assert count incremented

    // Assert database: Check if the like record was created in the pivot table
    $this->assertDatabaseHas('post_likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

// Test that an authenticated user can unlike a post they have previously liked
test('authenticated user can unlike a post', function () {
    // Arrange: Create user, post, and pre-like the post
    $user = User::factory()->create();
    $post = Post::factory()->create();
    $user->likedPosts()->attach($post->id); // Set up the initial liked state in the database

    // Act & Assert: Use Livewire test helper
    actingAs($user);

    Livewire::test(PostCardSection::class, ['post' => $post, 'show' => 'feed'])
        ->assertSet('isLiked', true) // Assert initial state is liked
        ->assertSet('likesCount', 1) // Assert initial count is 1
        ->call('toggleLike') // Call the unlike action
        ->assertSet('isLiked', false) // Assert state updated to not liked
        ->assertSet('likesCount', 0); // Assert count decremented

    // Assert database: Check that the like record was removed
    $this->assertDatabaseMissing('post_likes', [
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);
});

// Test that a guest (unauthenticated user) cannot like a post and is redirected
test('guest cannot like a post', function () {
    // Arrange: Create a post
    $post = Post::factory()->create();

    // Act & Assert: Mount the component without actingAs(user)
    Livewire::test(PostCardSection::class, ['post' => $post, 'show' => 'feed'])
        ->call('toggleLike') // Attempt to call the like action
        ->assertRedirect(route('login')); // Assert the user is redirected to the login page

    // Assert database: Ensure no like record was created
    $this->assertDatabaseMissing('post_likes', [
        'post_id' => $post->id,
    ]);
});
