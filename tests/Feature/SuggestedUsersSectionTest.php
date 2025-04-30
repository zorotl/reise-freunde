<?php

use App\Models\User;
use App\Models\Hobby;
use App\Models\TravelStyle;
use Livewire\Livewire;
use App\Livewire\Dashboard\SuggestedUsersSection;
use App\Livewire\Dashboard\Overview; // Import the parent component to test interactions

test('suggested users section displays suggested users', function () {
    // Arrange: Create test users and interests
    $user = User::factory()->create();
    $suggestedUser1 = User::factory()->create();
    $suggestedUser2 = User::factory()->create();
    $unrelatedUser = User::factory()->create();

    $hobby1 = Hobby::factory()->create();
    $style1 = TravelStyle::factory()->create();

    // Assign shared interests to make them appear as suggestions
    $user->hobbies()->attach($hobby1);
    $user->travelStyles()->attach($style1);
    $suggestedUser1->hobbies()->attach($hobby1); // Shares a hobby
    $suggestedUser2->travelStyles()->attach($style1); // Shares a travel style

    // To test the child component in isolation, we simulate the data structure
    // that the parent component (Overview) would pass down.
    // The Overview component adds 'shared_hobbies_count' and 'shared_travel_styles_count' using withCount.
    $suggestedUsersCollection = collect([
        $suggestedUser1->load('additionalInfo')->toArray() + ['shared_hobbies_count' => 1, 'shared_travel_styles_count' => 0],
        $suggestedUser2->load('additionalInfo')->toArray() + ['shared_hobbies_count' => 0, 'shared_travel_styles_count' => 1],
    ]);

    // Act: Mount the SuggestedUsersSection component with the simulated data
    Livewire::actingAs($user)
        ->test(SuggestedUsersSection::class, ['suggestedUsers' => $suggestedUsersCollection])
        // Assert: Check if the component renders correctly and displays the expected users
        ->assertSee(__('Suggested Users'))
        ->assertSee($suggestedUser1->name) // Should see suggested user 1
        ->assertSee($suggestedUser2->name) // Should see suggested user 2
        ->assertDontSee($unrelatedUser->name); // Should not see unrelated user
});

test('clicking follow user button triggers parent action and refreshes data', function () {
    // Arrange: Create a user and a suggested user
    $user = User::factory()->create();
    $suggestedUser = User::factory()->create(['is_private' => false]); // Make suggested user public for easier testing

    // Create shared interests so the suggested user appears in the list loaded by the parent
    $hobby = Hobby::factory()->create();
    $user->hobbies()->attach($hobby);
    $suggestedUser->hobbies()->attach($hobby);

    // Act: Test the parent Overview component since the action is handled there
    $overviewComponent = Livewire::actingAs($user)->test(Overview::class);

    // Assert: Initially, the suggested user should be visible
    $overviewComponent->assertSee($suggestedUser->name);

    // Act: Call the followUser action on the parent component
    $overviewComponent->call('followUser', $suggestedUser->id);

    // Assert: Check if the user is now following the suggested user (or a request was sent)
    $user->refresh(); // Refresh the user model to get the latest relationship state
    expect($user->isFollowing($suggestedUser))->toBeTrue(); // Assuming public profile, direct follow

    // Assert that the suggested user is no longer in the suggested list after being followed
    // The loadData method in Overview should refresh the suggestedUsers property.
    $overviewComponent->assertDontSee($suggestedUser->name); // Suggested user should be gone from suggestions
    $overviewComponent->assertSee('Now following ' . $suggestedUser->name); // Check for flash message
});