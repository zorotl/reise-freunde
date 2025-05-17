<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Report;

class ReportResolved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Report $report)
    {
    }

    public function via($notifiable)
    {
        $channels = ['database'];

        if (
            method_exists($notifiable, 'wantsEmailNotifications') &&
            $notifiable->wantsEmailNotifications('report_resolved')
        ) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $statusText = ucfirst($this->report->status);

        return (new MailMessage)
            ->subject("Your report was {$statusText}")
            ->line("A moderator has {$this->report->status} your report.")
            ->action('View Reports', url('/reports/my'))
            ->line('Thanks for helping keep the community safe.');
    }

    public function toArray($notifiable)
    {
        $type = class_basename($this->report->reportable_type);
        $status = ucfirst($this->report->status);

        return [
            'title' => "{$type} Report {$status}",
            'body' => "Your report about a {$type} was {$status}.",
            'url' => '/reports/my',
        ];
    }
}
