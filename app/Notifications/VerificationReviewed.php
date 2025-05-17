<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class VerificationReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $status)
    {
        //
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (
            method_exists($notifiable, 'wantsEmailNotifications')
            && $notifiable->wantsEmailNotifications('verification_reviewed')
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $subject = $this->status === 'accepted'
            ? 'Your profile verification was accepted'
            : 'Your profile verification was rejected';

        $line = $this->status === 'accepted'
            ? 'Your profile is now verified and visible as trusted.'
            : 'Unfortunately, your profile verification was rejected. You can try again.';

        return (new MailMessage)
            ->subject($subject)
            ->line($line)
            ->action('View Profile', url('/profile'));
    }

    public function toArray($notifiable)
    {
        return [
            'title' => $this->status === 'accepted'
                ? 'Verification Accepted'
                : 'Verification Rejected',
            'body' => $this->status === 'accepted'
                ? 'Your profile verification was approved.'
                : 'Your profile verification was rejected.',
            'url' => '/profile',
        ];
    }
}
