<?php

use App\Livewire\Admin\Reports\ManageReports;
use App\Models\User;
use App\Models\Post;
use App\Models\Message;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function approvedUser(): User
{
    return User::factory()->create([
        'email_verified_at' => now(),
        'status' => 'approved',
        'approved_at' => now(),
    ]);
}

it('deletes post when accepting post report', function () {
    $admin = approvedUser();
    $author = User::factory()->create();
    $post = Post::factory()->create(['user_id' => $author->id]);
    $report = Report::factory()->create([
        'reportable_type' => Post::class,
        'reportable_id' => $post->id,
    ]);

    Livewire::actingAs($admin)
        ->test(ManageReports::class)
        ->call('acceptReport', $report->id);

    expect(Post::find($post->id))->toBeNull();
    expect(Report::find($report->id)->status)->toBe('accepted');
});

it('deletes message when accepting message report', function () {
    $admin = approvedUser();
    $sender = User::factory()->create();
    $message = Message::factory()->create(['sender_id' => $sender->id]);
    $report = Report::factory()->create([
        'reportable_type' => Message::class,
        'reportable_id' => $message->id,
    ]);

    Livewire::actingAs($admin)
        ->test(ManageReports::class)
        ->call('acceptReport', $report->id);

    expect(Message::find($message->id))->toBeNull();
    expect(Report::find($report->id)->status)->toBe('accepted');
});

it('marks user report as accepted but does not delete user', function () {
    $admin = approvedUser();
    $user = User::factory()->create();
    $report = Report::factory()->create([
        'reportable_type' => User::class,
        'reportable_id' => $user->id,
    ]);

    Livewire::actingAs($admin)
        ->test(ManageReports::class)
        ->call('acceptReport', $report->id);

    expect(User::find($user->id))->not->toBeNull();
    expect(Report::find($report->id)->status)->toBe('accepted');
});
