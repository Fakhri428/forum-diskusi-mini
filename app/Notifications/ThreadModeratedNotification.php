<?php

namespace App\Notifications;

use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ThreadModeratedNotification extends Notification
{
    use Queueable;

    protected $thread;
    protected $action;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Thread $thread, string $action, ?string $reason = null)
    {
        $this->thread = $thread;
        $this->action = $action;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = "Your thread has been {$this->action}";
        $message = (new MailMessage)
                    ->subject($subject)
                    ->line("Your thread \"{$this->thread->title}\" has been {$this->action}.");

        if ($this->reason) {
            $message->line("Reason: {$this->reason}");
        }

        return $message->action('View Thread', url('/threads/' . $this->thread->id))
                      ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'thread_id' => $this->thread->id,
            'thread_title' => $this->thread->title,
            'action' => $this->action,
            'reason' => $this->reason,
        ];
    }
}
