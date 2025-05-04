<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\BanHistory;
use Illuminate\Support\Facades\Auth;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

test('ban history record is created when user is banned', function () {
    // Arrange
    $userToBan = User::factory()->create();
    // Create admin using the 'has' relationship method with the grant factory state
    $admin = User::factory()->create(); // Create the user
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $grant = UserGrant::factory()->create(['user_id' => $userToBan->id, 'is_banned' => false]);

    $banReason = 'Violation of terms';
    $banExpiry = now()->addMonth();

    // Act
    actingAs($admin);
    $grant->is_banned = true;
    $grant->banned_reason = $banReason;
    $grant->is_banned_until = $banExpiry;
    $grant->save();

    // Assert
    assertDatabaseHas('ban_histories', [
        'user_id' => $userToBan->id,
        'banned_by' => $admin->id,
        'reason' => $banReason,
        'expires_at' => $banExpiry->format('Y-m-d H:i:s'),
    ]);
});

test('ban history record is not created if is_banned does not change to true', function () {
    // Arrange
    $user = User::factory()->create();
    // Create admin using the 'has' relationship method with the grant factory state
    $admin = User::factory()->create(); // Create the user
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $grant = UserGrant::factory()->create(['user_id' => $user->id, 'is_banned' => true]); // Start as banned

    actingAs($admin);

    // Act 1
    $grant->banned_reason = 'Updated reason';
    $grant->save();

    // Assert 1
    expect(BanHistory::where('user_id', $user->id)->count())->toBe(0);

    // Act 2
    $grant->is_banned = false;
    $grant->is_banned_until = null;
    $grant->banned_reason = null;
    $grant->save();

    // Assert 2
    expect(BanHistory::where('user_id', $user->id)->count())->toBe(0);

    // Act 3
    $grant->is_moderator = true;
    $grant->save();

    // Assert 3
    expect(BanHistory::where('user_id', $user->id)->count())->toBe(0);
});

test('ban history logs correctly when banned via edit modal', function () {
    // Arrange
    // Create admin using the 'has' relationship method with the grant factory state
    $admin = User::factory()->create(); // Create the user
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $userToBan = User::factory()->create();
    $grant = UserGrant::factory()->create(['user_id' => $userToBan->id, 'is_banned' => false]);
    $banReason = 'Modal Ban Reason';
    $banExpiry = now()->addDays(7);

    actingAs($admin);

    // Act
    $grant->forceFill([
        'is_banned' => true,
        'is_banned_until' => $banExpiry,
        'banned_reason' => $banReason,
    ]);
    $grant->save();

    // Assert
    assertDatabaseHas('ban_histories', [
        'user_id' => $userToBan->id,
        'banned_by' => $admin->id,
        'reason' => $banReason,
        'expires_at' => $banExpiry->format('Y-m-d H:i:s'),
    ]);
});