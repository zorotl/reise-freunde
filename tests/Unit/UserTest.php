<?php

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in(__DIR__);

// initials() Methode
it('returns correct initials', function () {
    $user = User::factory()->make([
        'firstname' => 'Max',
        'lastname' => 'Power',
    ]);

    expect($user->initials())->toBe('MP');
});

// isFollowing()
it('returns true if user is following another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $user->following()->attach($other->id, ['accepted_at' => now()]);

    expect($user->isFollowing($other))->toBeTrue();
});

it('returns false if user is not following another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    expect($user->isFollowing($other))->toBeFalse();
});

// hasSentFollowRequestTo()
it('detects sent follow request', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    // pending = accepted_at null
    $user->pendingFollowingRequests()->attach($other->id, ['accepted_at' => null]);

    expect($user->hasSentFollowRequestTo($other))->toBeTrue();
});

// hasPendingFollowRequestFrom()
it('detects incoming follow request', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    // other sendet Anfrage an user
    $other->pendingFollowingRequests()->attach($user->id, ['accepted_at' => null]);

    expect($user->hasPendingFollowRequestFrom($other))->toBeTrue();
});

// follow() und unfollow()
it('can follow and unfollow a public user', function () {
    $user = User::factory()->create();
    $target = User::factory()->create();

    // Simuliere nicht-private Profile
    $target->setRelation('additionalInfo', (object) ['is_private' => false]);

    $user->follow($target);

    expect($user->isFollowing($target))->toBeTrue();

    $user->unfollow($target);

    expect($user->isFollowing($target))->toBeFalse();
});

// acceptFollowRequest()
it('can accept a follow request', function () {
    $user = User::factory()->create();
    $requester = User::factory()->create();

    // Anfrage simulieren
    $requester->pendingFollowingRequests()->attach($user->id, ['accepted_at' => null]);

    $accepted = $user->acceptFollowRequest($requester);

    expect($accepted)->toBeTrue();
    expect($requester->isFollowing($user))->toBeTrue();
});

