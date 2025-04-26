<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Hobby;
use App\Models\TravelStyle;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;
use App\Livewire\Dashboard\Overview;
use Livewire\Livewire;
use Illuminate\Database\Eloquent\Collection;

test('dashboard requires authentication', function () {
    get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view the dashboard', function () {
    $user = User::factory()->create(); // Assumes email is verified by factory or verification is disabled

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSeeLivewire(Overview::class); // Check if the Livewire component is loaded
});

test('dashboard displays welcome message', function () {
    $user = User::factory()->create(['name' => 'Test User']);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertSee('Welcome back, Test User!');
});

test('dashboard displays feed posts from self and followed users', function () {
    $user = User::factory()->create();
    $followedUser = User::factory()->create();
    $unfollowedUser = User::factory()->create();

    // User follows $followedUser
    $user->following()->attach($followedUser->id, ['accepted_at' => now()]);

    // --- FIX: Ensure BOTH posts have a future expiry_date ---
    $userPost = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'My Own Post Title',
        'is_active' => true,
        'expiry_date' => now()->addMonth(), // Future date
    ]);
    $followedPost = Post::factory()->create([
        'user_id' => $followedUser->id,
        'title' => 'Followed User Post Title',
        'is_active' => true,
        'expiry_date' => now()->addWeek(), // Future date (cannot be null)
    ]);
    // --- END FIX ---

    $unfollowedPost = Post::factory()->create([
        'user_id' => $unfollowedUser->id,
        'title' => 'Unfollowed User Post Title',
        'is_active' => true,
        'expiry_date' => now()->addMonth(),
    ]);
    // Create an inactive post from followed user (should not be shown)
    $inactiveFollowedPost = Post::factory()->create(['user_id' => $followedUser->id, 'title' => 'Inactive Followed Post', 'is_active' => false]);
    // Create an expired post from followed user (should not be shown)
    $expiredFollowedPost = Post::factory()->create(['user_id' => $followedUser->id, 'title' => 'Expired Followed Post', 'expiry_date' => now()->subDay()]);


    actingAs($user)
        ->get(route('dashboard'))
        ->assertSee($userPost->title)
        ->assertSee($followedPost->title)
        ->assertDontSee($unfollowedPost->title)
        ->assertDontSee($inactiveFollowedPost->title)
        ->assertDontSee($expiredFollowedPost->title);
});

test('dashboard displays follower and following counts', function () {
    $user = User::factory()->create();
    $follower = User::factory()->create();
    $following = User::factory()->create();

    // $follower follows $user
    $follower->following()->attach($user->id, ['accepted_at' => now()]);
    // $user follows $following
    $user->following()->attach($following->id, ['accepted_at' => now()]);

    // Refresh user to ensure counts are loaded correctly if using loadCount in component
    $user->refresh();

    actingAs($user)
        ->get(route('dashboard'))
        ->assertSeeHtml('>1</span>') // Assumes follower count is 1
        ->assertSeeHtml('>1</span>') // Assumes following count is 1
        ->assertSee(__('Followers'))
        ->assertSee(__('Following'));
});

test('dashboard displays pending follow requests notification', function () {
    $user = User::factory()->create();
    // Ensure the requester name matches exactly what the failing test output shows
    $requester = User::factory()->create(['name' => 'Dr. Robert Gleason IV']);

    // $requester requests to follow $user
    $requester->pendingFollowingRequests()->attach($user->id);

    Livewire::actingAs($user)
        ->test(Overview::class)
        // Keep this assertion to confirm component state is correct after mount
        ->assertSet('pendingRequests', function (Collection $requests) use ($requester) {
            return $requests->isNotEmpty() && $requests->contains('id', $requester->id);
        })
        // --- FIX: Use assertSeeHtml to check for the specific rendered list item ---
        ->assertSeeHtml('<span class="text-gray-600 dark:text-gray-400">wants to follow you.</span>') // Check for the static part
        ->assertSeeHtml($requester->name); // Check for the dynamic name within the HTML
    // --- END FIX ---

});

test('dashboard displays suggested users based on shared interests', function () {
    $user = User::factory()->create();
    $suggestedUser = User::factory()->create();
    $otherUser = User::factory()->create(); // No shared interests

    $hobby1 = Hobby::factory()->create();
    $hobby2 = Hobby::factory()->create();
    $style1 = TravelStyle::factory()->create();

    // Assign interests
    $user->hobbies()->attach($hobby1);
    $user->travelStyles()->attach($style1);

    $suggestedUser->hobbies()->attach($hobby1); // Shared hobby
    $suggestedUser->travelStyles()->attach($style1); // Shared style

    $otherUser->hobbies()->attach($hobby2); // Different hobby

    // Ensure relationships are loaded before testing component logic that relies on them
    $user->load(['hobbies', 'travelStyles', 'following', 'pendingFollowingRequests']);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertSee(__('Suggested Users'))
        ->assertSee($suggestedUser->name) // Should see suggested user
        ->assertDontSee($otherUser->name); // Should not see user with no shared interests
});