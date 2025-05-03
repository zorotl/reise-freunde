<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Post;
use App\Models\PostReport;
use Livewire\Livewire;
use App\Livewire\Admin\Reports\ManagePostReports;

use function Pest\Laravel\actingAs;

test('admin can view pending reports', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);

    $reporter = User::factory()->create();
    $post = Post::factory()->create();
    $report = PostReport::factory()->create([
        'user_id' => $reporter->id,
        'post_id' => $post->id,
        'status' => 'pending',
        'reason' => 'Spam content',
    ]);

    actingAs($admin);

    Livewire::test(ManagePostReports::class)
        ->assertSee($post->title) // Might need Str::limit if title is long
        ->assertSee($reporter->name)
        ->assertSee('Spam content')
        ->assertSee('Pending');
});

test('admin can accept a report and soft delete the post', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $post = Post::factory()->create();
    $report = PostReport::factory()->create(['post_id' => $post->id, 'status' => 'pending']);

    actingAs($admin);

    Livewire::test(ManagePostReports::class)
        ->call('acceptReport', $report)
        ->assertDispatched('reportProcessed'); // Assert refresh event

    $report->refresh();
    $post->refresh();

    expect($report->status)->toBe('accepted');
    expect($report->processed_by)->toBe($admin->id);
    expect($report->processed_at)->not->toBeNull();
    expect($post->trashed())->toBeTrue(); // Check if post is soft deleted
});

test('admin can reject a report', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $post = Post::factory()->create();
    $report = PostReport::factory()->create(['post_id' => $post->id, 'status' => 'pending']);

    actingAs($admin);

    Livewire::test(ManagePostReports::class)
        ->call('rejectReport', $report)
        ->assertDispatched('reportProcessed');

    $report->refresh();
    $post->refresh();

    expect($report->status)->toBe('rejected');
    expect($report->processed_by)->toBe($admin->id);
    expect($report->processed_at)->not->toBeNull();
    expect($post->trashed())->toBeFalse(); // Post should not be deleted
});