<?php

namespace App\Notifications;

use App\Models\Message; // If you pass the message model before deletion
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class AdminForceDeletedMessageNotification extends Notification implements ShouldQueue // Implement ShouldQueue if you want to queue these
{
    use Queueable;

    protected string $messageSubject;
    protected int $originalMessageId; // Store ID for reference if needed
    protected string $actionPerformingAdminName;

    /**
     * Create a new notification instance.
     * @param string $messageSubject The subject of the deleted message
     * @param int $originalMessageId The ID of the original message
     * @param string $actionPerformingAdminName The name of the admin who performed the action
     */
    public function __construct(string $messageSubject, int $originalMessageId, string $actionPerformingAdminName, ?int $originalSenderId, ?int $originalReceiverId)
    {
        $this->messageSubject = $messageSubject;
        $this->originalMessageId = $originalMessageId;
        $this->actionPerformingAdminName = $actionPerformingAdminName;
        $this->originalSenderId = $originalSenderId;
        $this->originalReceiverId = $originalReceiverId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Check user's notification preferences if you have them
        // For now, defaulting to database
        // if ($notifiable instanceof User && $notifiable->notificationPreferences()->receiveAdminActionAlerts()) {
        //     return ['database', 'mail']; // Example
        // }
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     * Optional: if you want to send emails as well.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->subject(__('Message Deleted by Administrator'))
    //                 ->line(__('A message you were a participant in has been permanently deleted by an administrator.'))
    //                 ->line(__('Message Subject: ') . $this->messageSubject)
    //                 ->line(__('This action was performed by: ') . $this->actionPerformingAdminName)
    //                 ->line(__('If you have any questions, please contact support.'))
    //                 ->action(__('Visit Site'), url('/'));
    // }

    /**
     * Get the array representation of the notification.
     * This is what will be stored in the 'data' column of the notifications table.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $messageType = ($notifiable->id === $this->getOriginalSenderIdFromSomewhereIfNotPassed()) ? 'sent' : 'received'; // This needs a way to know if user was sender/receiver

        return [
            'message' => __("A message titled ':subject' that you {$messageType} was permanently deleted by administrator :adminName.", [
                'subject' => Str::limit($this->messageSubject, 50),
                'adminName' => $this->actionPerformingAdminName,
            ]),
            'original_message_id' => $this->originalMessageId, // For potential linking or reference, though message is gone
            'action_url' => route('mail.inbox'), // Generic link, as the message itself is gone
            'icon' => 'trash', // Suggests a FluxUI icon name
            'type' => 'admin_action', // Custom type for filtering if needed
        ];
    }

    /**
     * This method is a placeholder. You'd need to adjust how you determine
     * if the notifiable user was the sender or receiver.
     * One way is to pass senderId and receiverId to the constructor.
     */
    private function getOriginalSenderIdFromSomewhereIfNotPassed(): ?int
    {
        // This logic is illustrative. In a real scenario, you'd likely pass sender_id
        // to the notification constructor if you need to differentiate the message for sender/receiver.
        // For now, toArray doesn't use it directly in the string.
        return null;
    }


    /**
     * Get the array representation of the notification for database storage.
     * This will be stored in the 'data' column of the notifications table.
     */
    public function toDatabase(object $notifiable): array
    {
        // Determine if the notifiable user was the sender or receiver of the original message.
        // This requires passing sender_id and receiver_id to the constructor.
        // For simplicity in this example, we'll use a generic message.
        // In a real implementation, you'd pass sender_id and receiver_id to constructor.
        // Let's assume for now we can't easily differentiate here without more info.
        // So, using a more generic message.

        return [
            'title' => __('Message Deleted by Admin'),
            'body' => __("A message titled ':subject' you were involved in was permanently deleted by administrator :adminName.", [
                'subject' => Str::limit($this->messageSubject, 50),
                'adminName' => $this->actionPerformingAdminName,
            ]),
            'original_message_id' => $this->originalMessageId,
            'action_url' => route('mail.inbox'), // Generic link to their inbox
            'icon' => 'shield-exclamation', // An icon indicating an admin action or alert
            'category' => 'admin_message_action', // For grouping notifications
        ];
    }

    public function getMessageSubject(): string
    {
        return $this->messageSubject;
    }

    public function getOriginalMessageId(): int
    {
        return $this->originalMessageId;
    }

    public function getActionPerformingAdminName(): string
    {
        return $this->actionPerformingAdminName;
    }

    public function getOriginalSenderId(): ?int
    {
        return $this->originalSenderId;
    }

    public function getOriginalReceiverId(): ?int
    {
        return $this->originalReceiverId;
    }

}