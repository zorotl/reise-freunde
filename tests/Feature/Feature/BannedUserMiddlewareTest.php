<?php

use App\Models\User;
use App\Models\UserGrant;
use Illuminate\Support\Carbon;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post; // <-- Add post for logout test

// Helper function to create a user with a specific ban status
function createTestUserWithBan(bool $isBanned, ?string $bannedUntil = null, ?string $reason = null): User
{
    $user = User::factory()->create();
    // Use updateOrCreate and store the result
    $grant = UserGrant::updateOrCreate(
        ['user_id' => $user->id],
        [
            'is_banned' => $isBanned,
            'is_banned_until' => $bannedUntil ? Carbon::parse($bannedUntil) : null,
            'banned_reason' => $reason, // Save the reason
            'is_admin' => false,
            'is_moderator' => false,
        ]
    );

    // Force refresh the user model and explicitly load the grant relationship
    // This ensures the test gets the latest data including the grant changes.
    $user->refresh()->load('grant');

    return $user;
}

test('non-banned user can access dashboard', function () {
    $user = createTestUserWithBan(false); // Not banned

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk(); // Should be allowed
});

test('permanently banned user is redirected to banned page', function () {
    $reason = 'Permanent ban testing';
    $user = createTestUserWithBan(true, null, $reason); // Pass the reason

    actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('banned'));

    actingAs($user)
        ->get(route('banned'))
        ->assertOk()
        ->assertSee('Account Suspended')
        ->assertSeeText($reason, false) // Use assertSeeText, escape=false
        ->assertSee('Indefinitely');
});

test('temporarily banned user (active ban) is redirected to banned page', function () {
    $banEndDate = now()->addDay()->format('Y-m-d H:i:s');
    $reason = 'Temporary ban testing';
    $user = createTestUserWithBan(true, $banEndDate, $reason); // Pass the reason

    actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('banned'));

    actingAs($user)
        ->get(route('banned'))
        ->assertOk()
        ->assertSee('Account Suspended')
        ->assertSeeText($reason, false) // Use assertSeeText, escape=false
        ->assertSee(Carbon::parse($banEndDate)->format('Y-m-d H:i:s'));
});

test('temporarily banned user (expired ban) can access dashboard and is unbanned', function () {
    $user = createTestUserWithBan(true, now()->subDay()->format('Y-m-d H:i:s')); // is_banned = true, until = past

    actingAs($user)
        ->get(route('dashboard'))
        ->assertOk(); // Should be allowed

    // Verify the user is automatically unbanned in the database
    $user->refresh()->load('grant');
    expect($user->grant->is_banned)->toBeFalse();
    expect($user->grant->is_banned_until)->toBeNull();
});

test('banned user can access logout route', function () {
    $user = createTestUserWithBan(true); // Permanently banned

    actingAs($user)
        ->post(route('logout')) // Simulate logout POST request
        ->assertRedirect('/'); // Should successfully redirect after logout

    // Assert the user is logged out
    expect(Auth::check())->toBeFalse();
});

test('non-banned user accessing banned page is redirected', function () {
    $user = createTestUserWithBan(false); // Not banned

    actingAs($user)
        ->get(route('banned'))
        ->assertRedirect(route('dashboard')); // Should be redirected away
});

test('guest user cannot access banned page', function () {
    get(route('banned'))
        ->assertRedirect(route('login')); // Guests should be sent to login
});

test('banned user middleware automatically unbans if ban expired', function () {
    // Arrange: Create a user banned until yesterday
    $user = User::factory()->create();
    $grant = UserGrant::factory()->create([
        'user_id' => $user->id,
        'is_banned' => true,
        'is_banned_until' => now()->subDay(),
    ]);

    // Act: Simulate the user making a request (e.g., to dashboard)
    actingAs($user)->get(route('dashboard'));

    // Assert: Check the grant record in the database
    $grant->refresh();
    expect($grant->is_banned)->toBeFalse()
        ->and($grant->is_banned_until)->toBeNull();
});