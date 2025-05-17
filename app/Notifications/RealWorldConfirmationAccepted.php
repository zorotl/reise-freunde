<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class RealWorldConfirmationAccepted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public User $confirmedUser)
    {
        //
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (
            method_exists($notifiable, 'notificationEnabled') &&
            $notifiable->notificationEnabled('real_world_confirmation')
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Real-World Confirmation was accepted')
            ->line("{$this->confirmedUser->name} has confirmed they know you in real life.")
            ->action('View Profile', url('/profile/' . $this->confirmedUser->id))
            ->line('Thanks for helping keep the platform safe.');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Real-World Confirmation Accepted',
            'body' => 'Another user has accepted your confirmation request.',
            'url' => '/profile',
        ];
    }
}
