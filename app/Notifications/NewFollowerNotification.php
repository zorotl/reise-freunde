<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewFollowerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $follower;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $follower)
    {
        $this->follower = $follower;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', /* 'database' */];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $profileUrl = route('user.profile', $this->follower->id);

        return (new MailMessage)
            ->subject(__('You Have a New Follower!'))
            ->line(__(':name started following you.', ['name' => $this->follower->name]))
            ->action(__('View Profile'), $profileUrl)
            ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->name,
            'message' => $this->follower->name . ' started following you.',
        ];
    }
}