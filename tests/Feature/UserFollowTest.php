<?php

use App\Models\User;
use function Pest\Laravel\actingAs;
use App\Livewire\User\Search;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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

it('only affects the targeted user in Livewire follow', function () {
    $current = User::factory()->create(['email_verified_at' => now(), 'status' => 'approved', 'approved_at' => now()]);
    $target1 = User::factory()->create();
    $target2 = User::factory()->create();

    Livewire::actingAs($current)
        ->test(Search::class)
        ->call('followUser', $target1->id);

    expect($current->isFollowing($target1))->toBeTrue();
    expect($current->isFollowing($target2))->toBeFalse();
});

it('follows public or creates request for private user (Livewire)', function () {
    $follower = User::factory()->create(['email_verified_at' => now(), 'status' => 'approved', 'approved_at' => now()]);
    $private = User::factory()
        ->hasAdditionalInfo(['is_private' => true])
        ->create();
    $public = User::factory()
        ->hasAdditionalInfo(['is_private' => false])
        ->create();



    Livewire::actingAs($follower)->test(Search::class)->call('followUser', $public->id);
    Livewire::actingAs($follower)->test(Search::class)->call('followUser', $private->id);

    expect($follower->isFollowing($public))->toBeTrue();
    expect($follower->hasSentFollowRequestTo($private))->toBeTrue();
});