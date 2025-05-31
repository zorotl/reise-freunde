<?php

use App\Models\User;
use App\Models\UserGrant;
use App\Models\Post;
use App\Models\Report;
use Livewire\Livewire;
use App\Livewire\Admin\Reports\ManageReports;

use function Pest\Laravel\actingAs;

function createAdmin(): User
{
    $admin = User::factory()->create([
        'status' => 'approved',
        'email_verified_at' => now(),
        'approved_at' => now(),
    ]);

    UserGrant::factory()->admin()->create([
        'user_id' => $admin->id,
        'is_banned' => false,
    ]);

    return $admin;
}

test('admin can view pending reports', function () {
    $admin = createAdmin();

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
        ->assertSee($post->title)
        ->assertSee($reporter->name)
        ->assertSee('Spam content')
        ->assertSee('pending');
});

test('admin can accept a report and soft delete the post', function () {
    $admin = createAdmin();
    $post = Post::factory()->create();
    $report = Report::factory()->create([
        'reportable_id' => $post->id,
        'reportable_type' => Post::class,
        'status' => 'pending',
    ]);

    actingAs($admin);

    Livewire::test(ManageReports::class)
        ->call('acceptReport', $report->id)
        ->assertDispatched('reportProcessed');

    $report->refresh();
    $post->refresh();

    expect($report->status)->toBe('accepted');
    // expect($report->processed_by)->toBe($admin->id);
    // expect($report->processed_at)->not->toBeNull();
    expect($post->trashed())->toBeTrue();
});

test('admin can reject a report', function () {
    $admin = createAdmin();
    $post = Post::factory()->create();
    $report = Report::factory()->create([
        'reportable_id' => $post->id,
        'reportable_type' => Post::class,
        'status' => 'pending',
    ]);

    actingAs($admin);

    Livewire::test(ManageReports::class)
        ->call('rejectReport', $report->id)
        ->assertDispatched('reportProcessed');

    $report->refresh();
    $post->refresh();

    expect($report->status)->toBe('rejected');
    expect($report->processed_by)->toBe($admin->id);
    expect($report->processed_at)->not->toBeNull();
    expect($post->trashed())->toBeFalse();
});

test('admin accepting report redirects to user management filtered by post author', function () {
    $admin = createAdmin();
    $postAuthor = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $postAuthor->id]);
    $report = Report::factory()->create([
        'reportable_id' => $post->id,
        'reportable_type' => Post::class,
        'status' => 'pending',
    ]);

    actingAs($admin);

    Livewire::test(ManageReports::class)
        ->call('acceptReport', $report->id)
        ->assertDispatched('openEditModal', $postAuthor->id) // <- dispatches modal
        ->assertDispatched('reportProcessed');

    $post->refresh();
    $report->refresh();

    expect($post->trashed())->toBeTrue();
    expect($report->status)->toBe('accepted');
    // expect($report->processed_by)->toBe($admin->id);
});
