<?php

use App\Models\User;
use function Pest\Laravel\actingAs;

it('allows a user to follow another user', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    actingAs($user)
        ->post(route('user.follow', $other))
        ->assertRedirect();

    expect($user->fresh()->isFollowing($other))->toBeTrue();
});

it('allows a user to unfollow someone they follow', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $user->follow($other);

    actingAs($user)
        ->post(route('user.unfollow', $other))
        ->assertRedirect();

    expect($user->fresh()->isFollowing($other))->toBeFalse();
});

it('prevents a user from following themselves', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('user.follow', $user))
        ->assertForbidden();

    expect($user->following()->count())->toBe(0);
});
