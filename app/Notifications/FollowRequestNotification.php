<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User; // Import User model

class FollowRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $requester;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $requester)
    {
        $this->requester = $requester;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', /* 'database' */]; // Add 'database' if you want in-app notifications too
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $profileUrl = route('user.profile', $this->requester->id);
        $requestsUrl = route('user.follow-requests'); // Link to the requests page

        return (new MailMessage)
            ->subject(__('New Follow Request'))
            ->line(__(':name wants to follow you.', ['name' => $this->requester->name]))
            ->line(__('Since your profile is private, you need to approve their request.'))
            ->action(__('View Request'), $requestsUrl) // Or link directly to requester profile: $profileUrl
            ->line(__('Thank you for using our application!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // For database channel if used later
        return [
            'requester_id' => $this->requester->id,
            'requester_name' => $this->requester->name,
            'message' => $this->requester->name . ' wants to follow you.',
        ];
    }
}