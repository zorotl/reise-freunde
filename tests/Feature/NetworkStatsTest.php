<?php

use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Dashboard\NetworkStats;

test('network stats component displays follower and following counts', function () {
    // Arrange: Create a user and setup followers/following relationships
    $user = User::factory()->create();
    $follower = User::factory()->create();
    $following = User::factory()->create();

    // $follower follows $user (accepted follow)
    $follower->following()->attach($user->id, ['accepted_at' => now()]);
    // $user follows $following (accepted follow)
    $user->following()->attach($following->id, ['accepted_at' => now()]);

    // Load the updated follower and following counts on the user model
    $user->loadCount(['followers', 'following']);

    // Act: Mount the NetworkStats component, passing the counts and user
    Livewire::actingAs($user)
        ->test(NetworkStats::class, [
            'user' => $user, // Pass the user model for link generation
            'followerCount' => $user->followers_count, // Pass the calculated follower count
            'followingCount' => $user->following_count, // Pass the calculated following count
        ])
        // Assert: Check if the component renders correctly and displays the counts
        ->assertSee(__('Your Network'))
        ->assertSeeHtml('>1</span>') // Assumes follower count is 1. Use assertSee("1") for exact text matching
        ->assertSee(__('Followers'))
        ->assertSeeHtml('>1</span>') // Assumes following count is 1
        ->assertSee(__('Following'));
});