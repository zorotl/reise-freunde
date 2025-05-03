<?php

use App\Models\User;
use App\Models\Post;
use App\Models\PostReport;
use App\Livewire\ReportPostModal;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('authenticated user can report a post', function () {
    $reporter = User::factory()->create();
    $postOwner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    actingAs($reporter);

    Livewire::test(ReportPostModal::class)
        ->call('openModal', postId: $post->id, postTitle: $post->title)
        ->assertSet('showModal', true)
        ->set('reason', 'This post is inappropriate.')
        ->call('submitReport')
        ->assertDispatched('notify', ['message' => 'Post reported successfully.', 'type' => 'success']) // Assuming you dispatch this event
        ->assertSet('showModal', false);

    // Assert report exists in the database
    expect(PostReport::where('post_id', $post->id)->where('user_id', $reporter->id)->exists())->toBeTrue();
});

test('user cannot report the same post multiple times while pending', function () {
    $reporter = User::factory()->create();
    $postOwner = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postOwner->id]);

    // First report
    PostReport::create([
        'user_id' => $reporter->id,
        'post_id' => $post->id,
        'status' => 'pending',
    ]);

    actingAs($reporter);

    Livewire::test(ReportPostModal::class)
        ->call('openModal', postId: $post->id, postTitle: $post->title)
        ->set('reason', 'Reporting again')
        ->call('submitReport')
        ->assertHasErrors('general'); // Assert the specific error for duplicate report

    // Assert only one report exists
    expect(PostReport::where('post_id', $post->id)->where('user_id', $reporter->id)->count())->toBe(1);
});
