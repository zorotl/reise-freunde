<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Post;
use App\Models\Report;
use Livewire\Livewire;
use App\Livewire\Admin\Reports\ManageReports;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('admin can view pending reports', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);

    $reporter = User::factory()->create();
    $post = Post::factory()->create();
    $report = Report::factory()->create([
        'reporter_id' => $reporter->id,
        'reportable_id' => $post->id,
        'reportable_type' => Post::class,
        'status' => 'pending',
        'reason' => 'Spam content',
    ]);

    actingAs($admin);

    Livewire::test(ManageReports::class)
        ->assertSee($post->title) // Might need Str::limit if title is long
        ->assertSee($reporter->name)
        ->assertSee('Spam content')
        ->assertSee('pending');
});

test('admin can accept a report and soft delete the post', function () {
    $admin = User::factory()->create();
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $post = Post::factory()->create();
    $report = Report::factory()->create(['reportable_id' => $post->id, 'reportable_type' => Post::class, 'status' => 'pending']);

    actingAs($admin);

    Livewire::test(ManageReports::class)
        ->call('acceptReport', $report->id) // Pass ID
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
    $report = Report::factory()->create(['reportable_id' => $post->id, 'reportable_type' => Post::class, 'status' => 'pending']);

    actingAs($admin);

    Livewire::test(ManageReports::class)
        ->call('rejectReport', $report->id) // Pass ID
        ->assertDispatched('reportProcessed');

    $report->refresh();
    $post->refresh();

    expect($report->status)->toBe('rejected');
    expect($report->processed_by)->toBe($admin->id);
    expect($report->processed_at)->not->toBeNull();
    expect($post->trashed())->toBeFalse(); // Post should not be deleted
});

test('admin accepting report redirects to user management filtered by post author', function () {
    // Arrange
    // Create admin using the 'has' relationship method with the grant factory state
    $admin = User::factory()->create(); // Create the user
    UserGrant::factory()->admin()->create(['user_id' => $admin->id]);
    $postAuthor = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    $report = Report::factory()->create(['reportable_id' => $post->id, 'reportable_type' => Post::class, 'status' => 'pending']);

    actingAs($admin);

    // Act
    Livewire::test(ManageReports::class)
        ->call('acceptReport', $report->id) // Pass ID
        ->assertRedirect(route('admin.users', ['filterUserId' => $postAuthor->id]));

    // Assert (optional but good)
    $post->refresh();
    $report->refresh();
    expect($post->trashed())->toBeTrue();
    expect($report->status)->toBe('accepted');
    expect($report->processed_by)->toBe($admin->id);
});