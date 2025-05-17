<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RealWorldConfirmationRequested extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public User $requester)
    {
        //
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (
            method_exists($notifiable, 'notificationEnabled') &&
            $notifiable->notificationEnabled('real_world_confirmation_request')
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Real-World Confirmation Request')
            ->line("{$this->requester->name} has requested a confirmation from you.")
            ->action('Review Request', url('/profile/confirmations'))
            ->line('You can accept or reject the request.');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Confirmation Request Received',
            'body' => "{$this->requester->name} has requested a real-world confirmation from you.",
            'url' => '/profile/confirmations',
        ];
    }
}