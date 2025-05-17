<?php

use App\Models\User;
use App\Models\Report;
use App\Notifications\RealWorldConfirmationAccepted;
use App\Notifications\RealWorldConfirmationRequested;
use App\Notifications\YouConfirmedSomeone;
use App\Notifications\VerificationReviewed;
use App\Notifications\ReportResolved;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('sends RealWorldConfirmationRequested if enabled', function () {
    $requester = User::factory()->create();
    $confirmer = User::factory()->create([
        'notification_preferences' => ['real_world_confirmation_request' => true],
    ]);

    $confirmer->notify(new RealWorldConfirmationRequested($requester));

    Notification::assertSentTo($confirmer, RealWorldConfirmationRequested::class);
});

it('does not send RealWorldConfirmationRequested if disabled', function () {
    $requester = User::factory()->create();
    $confirmer = User::factory()->create([
        'notification_preferences' => ['real_world_confirmation_request' => false],
    ]);

    $confirmer->notify(new RealWorldConfirmationRequested($requester));

    Notification::assertNotSentTo($confirmer, RealWorldConfirmationRequested::class, function ($notification, $channels) {
        return in_array('mail', $channels);
    });
});

it('sends RealWorldConfirmationAccepted if enabled', function () {
    $confirmedUser = User::factory()->create();
    $requester = User::factory()->create([
        'notification_preferences' => ['real_world_confirmation' => true],
    ]);

    $requester->notify(new RealWorldConfirmationAccepted($confirmedUser));

    Notification::assertSentTo($requester, RealWorldConfirmationAccepted::class);
});

it('sends YouConfirmedSomeone if enabled', function () {
    $confirmed = User::factory()->create();
    $confirmer = User::factory()->create([
        'notification_preferences' => ['real_world_confirmation' => true],
    ]);

    $confirmer->notify(new YouConfirmedSomeone($confirmed));

    Notification::assertSentTo($confirmer, YouConfirmedSomeone::class);
});

it('sends VerificationReviewed if enabled', function () {
    $user = User::factory()->create([
        'notification_preferences' => ['verification_reviewed' => true],
    ]);

    $user->notify(new VerificationReviewed('accepted'));

    Notification::assertSentTo($user, VerificationReviewed::class);
});

it('sends ReportResolved if enabled', function () {
    $reporter = User::factory()->create([
        'notification_preferences' => ['report_resolved' => true],
    ]);

    $report = new Report([
        'reporter_id' => $reporter->id,
        'status' => 'accepted',
        'reportable_type' => \App\Models\Post::class,
        'reportable_id' => 999,
    ]);

    $reporter->notify(new ReportResolved($report));

    Notification::assertSentTo($reporter, ReportResolved::class);
});

