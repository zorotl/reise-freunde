<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BugReportReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $status)
    {
    }

    public function via($notifiable)
    {
        // Always send mail, add database channel for users
        $channels = ['mail'];
        if (method_exists($notifiable, 'getMorphClass')) {
            $channels[] = 'database';
        }
        return $channels;
    }

    public function toMail($notifiable)
    {
        $statusText = ucfirst($this->status);

        return (new MailMessage)
            ->subject("Bug Report {$statusText}")
            ->line('Thank you for your bug report.')
            ->line("Your report was {$statusText}.")
            ->action('Visit Site', url('/'));
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Bug Report '.ucfirst($this->status),
            'body' => 'Your bug report was '.ucfirst($this->status).'.',
            'url' => '/',
        ];
    }
}
