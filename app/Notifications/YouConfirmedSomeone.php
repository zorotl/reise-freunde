<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class YouConfirmedSomeone extends Notification implements ShouldQueue
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
            method_exists($notifiable, 'wantsEmailNotifications')
            && $notifiable->wantsEmailNotifications('real_world_confirmation')
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You confirmed a user')
            ->line("You’ve successfully confirmed that you know {$this->confirmedUser->name} in real life.")
            ->action('View Their Profile', url('/profile/' . $this->confirmedUser->id))
            ->line('Thank you for building trust in the community!');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'You confirmed a user',
            'body' => "You confirmed that you know {$this->confirmedUser->name}.",
            'url' => '/profile/' . $this->confirmedUser->id,
        ];
    }
}
