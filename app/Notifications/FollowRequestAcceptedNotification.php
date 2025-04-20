<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User; // Import User model

class FollowRequestAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $accepter; // The user who accepted the request

    /**
     * Create a new notification instance.
     */
    public function __construct(User $accepter)
    {
        $this->accepter = $accepter;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // $notifiable will be the user whose request was accepted
        return ['mail', /* 'database' */];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // $notifiable is the user receiving the email (the original requester)
        $profileUrl = route('user.profile', $this->accepter->id); // Link to the accepter's profile

        return (new MailMessage)
            ->subject(__('Follow Request Accepted'))
            ->line(__(':name has accepted your follow request.', ['name' => $this->accepter->name]))
            ->action(__('View :name\'s Profile', ['name' => $this->accepter->name]), $profileUrl)
            ->line(__('You are now following them.'));
    }

    /**
     * Get the array representation of the notification.
     * (For database channel if you add it to via())
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'accepter_id' => $this->accepter->id,
            'accepter_name' => $this->accepter->name,
            'message' => $this->accepter->name . ' accepted your follow request.',
        ];
    }
}