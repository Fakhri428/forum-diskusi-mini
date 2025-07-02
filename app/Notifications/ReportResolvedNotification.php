<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportResolvedNotification extends Notification
{
    use Queueable;

    protected $report;
    protected $status;
    protected $action;
    protected $reason;
    protected $contentType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report, string $status, string $action, string $reason, string $contentType = null)
    {
        $this->report = $report;
        $this->status = $status;
        $this->action = $action;
        $this->reason = $reason;
        $this->contentType = $contentType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
                    ->subject('Report Resolution Notification')
                    ->greeting('Hello ' . $notifiable->name);

        if ($this->status === 'approved') {
            $message->line('Your report has been reviewed and approved.')
                   ->line("Action taken: {$this->action}")
                   ->line("Reason: {$this->reason}");
        } elseif ($this->status === 'rejected') {
            $message->line('Your report has been reviewed and rejected.')
                   ->line("Reason: {$this->reason}");
        } elseif ($this->status === 'content_moderated') {
            $message->line("Your {$this->contentType} has been moderated.")
                   ->line("Action taken: {$this->action}")
                   ->line("Reason: {$this->reason}");
        }

        return $message->action('View Details', url('/reports/' . $this->report->id))
                      ->line('Thank you for helping us maintain our community standards.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'status' => $this->status,
            'action' => $this->action,
            'reason' => $this->reason,
            'content_type' => $this->contentType,
        ];
    }
}
