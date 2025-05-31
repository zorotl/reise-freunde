<?php

use App\Models\User;
use App\Models\UserAdditionalInfo;
use App\Models\UserGrant; // Added UserGrant
use App\Livewire\Mail\MessageCompose;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

// Helper function to create a user with additional info and grant consistently
function createUserWithMessageData(array $userData = [], array $additionalInfoData = [], array $grantData = []): User
{
    $user = User::factory()->create(array_merge([
        'status' => 'approved',            // ✅ ensure approved
        'email_verified_at' => now(),
        'approved_at' => now(),
    ], $userData));

    UserAdditionalInfo::factory()->create(array_merge([
        'user_id' => $user->id,
        'username' => 'defaultuser' . $user->id,
        'birthday' => now()->subYears(25), // Optional: ensure age filters
        'nationality' => 'CH',             // Optional default
    ], $additionalInfoData));

    UserGrant::factory()->create(array_merge([
        'user_id' => $user->id,
        'is_banned' => false,
    ], $grantData));

    return $user->load(['additionalInfo', 'grant']);
}

test('can search for users by username', function () {
    $currentUser = createUserWithMessageData();
    Auth::login($currentUser);

    $userToFind = createUserWithMessageData(
        ['firstname' => 'UserToFind', 'lastname' => 'ByUsername'],
        ['username' => 'FindThisExactUser'] // Specific username for this test
    );
    // Add this log to confirm $userToFind is set up as expected
    logger()->info('UserToFind setup:', ['id' => $userToFind->id, 'username' => $userToFind->additionalInfo?->username, 'name' => $userToFind->name]);


    createUserWithMessageData([], ['username' => 'AnotherUser']);

    Livewire::test(MessageCompose::class)
        ->set('search', 'FindThisEx') // Partial search for the specific username
        ->assertSet('searchResults', function ($results) use ($userToFind) {
            // LOGGING
            logger()->info('SEARCH BY USERNAME TEST - Actual searchResults:', ['results' => $results]);
            logger()->info('SEARCH BY USERNAME TEST - Expecting to find user:', ['id' => $userToFind->id, 'username' => 'FindThisExactUser', 'full_name' => $userToFind->firstname . ' ' . $userToFind->lastname]);

            if (count($results) !== 1) {
                logger()->error('SEARCH BY USERNAME TEST - Expected 1 result, got ' . count($results));
                return false;
            }
            $foundUser = $results[0];
            if ($foundUser['id'] !== $userToFind->id) {
                logger()->error('SEARCH BY USERNAME TEST - ID mismatch. Expected ' . $userToFind->id . ', got ' . $foundUser['id']);
                return false;
            }
            // The display_name from the component's map function should be the username
            if ($foundUser['display_name'] !== 'FindThisExactUser') {
                logger()->error('SEARCH BY USERNAME TEST - Display name mismatch. Expected "FindThisExactUser", got "' . $foundUser['display_name'] . '"');
                return false;
            }
            return true;
        });
});

test('can search for users by firstname', function () {
    $currentUser = createUserWithMessageData();
    Auth::login($currentUser);

    // User to find by firstname, ensure username is distinct or absent for clarity
    $userToFind = createUserWithMessageData(
        ['firstname' => 'Johnathan', 'lastname' => 'Doelittle'],
        ['username' => 'jdoelittle_unique'] // Give a username
    );
    createUserWithMessageData(['firstname' => 'Jane', 'lastname' => 'Smith'], ['username' => 'janesmith']);

    Livewire::test(MessageCompose::class)
        ->set('search', 'Johnath') // Search by firstname
        ->assertSet('searchResults', function ($results) use ($userToFind) {
            if (count($results) !== 1) {
                logger('Search by firstname results (expected 1):', $results);
                return false;
            }
            // The display_name should be the username since it's present
            return $results[0]['id'] === $userToFind->id && $results[0]['display_name'] === 'jdoelittle_unique';
        });
});


test('search excludes current user', function () {
    $currentUser = createUserWithMessageData(['firstname' => 'SelfSearcher'], ['username' => 'selfsearcher_username']);
    Auth::login($currentUser);

    Livewire::test(MessageCompose::class)
        ->set('search', 'SelfS')
        ->assertSet('searchResults', []);
});

test('search excludes banned users', function () {
    $currentUser = createUserWithMessageData();
    Auth::login($currentUser);

    // Banned user: their name/username should match the search term "BannedSearch"
    $bannedUser = createUserWithMessageData(
        ['firstname' => 'BannedSearch'],
        ['username' => 'banned_user_name'],
        ['is_banned' => true] // Set banned status via grantData in helper
    );

    // Not banned user: their name/username should also match "BannedSearch"
    $notBannedUser = createUserWithMessageData(
        ['firstname' => 'BannedSearchVisible'],
        ['username' => 'not_banned_user_name']
        // grantData defaults to not banned
    );

    Livewire::test(MessageCompose::class)
        ->set('search', 'BannedSearch')
        ->assertSet('searchResults', function ($results) use ($notBannedUser, $bannedUser) {
            $foundNotBanned = collect($results)->contains('id', $notBannedUser->id);
            $foundBanned = collect($results)->contains('id', $bannedUser->id);

            if (!$foundNotBanned || $foundBanned || count($results) !== 1) {
                logger('Search excludes banned users - Actual Results:', $results);
                logger('Not Banned User ID Expected:', ['id' => $notBannedUser->id, 'display_name' => $notBannedUser->additionalInfo->username]);
                logger('Banned User ID NOT Expected:', ['id' => $bannedUser->id, 'display_name' => $bannedUser->additionalInfo->username]);
            }
            return $foundNotBanned && !$foundBanned && count($results) === 1;
        });
});


test('selectRecipient correctly sets receiver id and name', function () {
    $currentUser = createUserWithMessageData();
    Auth::login($currentUser);

    $userToSelect = createUserWithMessageData([], ['username' => 'SelectableUser']);

    Livewire::test(MessageCompose::class)
        ->call('selectRecipient', $userToSelect->id, 'SelectableUser')
        ->assertSet('receiver_id', $userToSelect->id)
        ->assertSet('selectedRecipientName', 'SelectableUser')
        ->assertSet('search', '')
        ->assertSet('showResults', false);
});

test('deselectRecipient clears recipient unless fixed', function () {
    $currentUser = createUserWithMessageData();
    Auth::login($currentUser);

    $recipient = createUserWithMessageData(
        ['firstname' => 'Initial', 'lastname' => 'Person'],
        ['username' => 'InitialRecipientUsername']
    );

    // Test when not fixed
    Livewire::test(MessageCompose::class)
        ->call('selectRecipient', $recipient->id, 'InitialRecipientUsername')
        ->assertSet('selectedRecipientName', 'InitialRecipientUsername')
        ->call('deselectRecipient')
        ->assertSet('receiver_id', null)
        ->assertSet('selectedRecipientName', '');

    // Test when fixed
    $fixedRecipient = createUserWithMessageData(
        ['firstname' => 'Fixed', 'lastname' => 'RecipientName'], // Name used if username is null
        ['username' => 'FixedUserIsTheUsername'] // This username should be picked by mount
    );

    Livewire::test(MessageCompose::class, ['receiverId' => $fixedRecipient->id, 'fixReceiver' => true])
        ->assertSet('receiver_id', $fixedRecipient->id)
        // The mount method should set selectedRecipientName using the username first
        ->assertSet('selectedRecipientName', 'FixedUserIsTheUsername')
        ->call('deselectRecipient') // Try to deselect
        ->assertSet('receiver_id', $fixedRecipient->id) // Should remain
        ->assertSet('selectedRecipientName', 'FixedUserIsTheUsername'); // Should remain
});