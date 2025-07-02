<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentModeratedNotification extends Notification
{
    use Queueable;

    protected $comment;
    protected $action;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, string $action, ?string $reason)
    {
        $this->comment = $comment;
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
        $message = (new MailMessage)
                    ->subject('Your Comment Has Been Moderated')
                    ->line('Your comment has been ' . $this->action . '.');

        if ($this->reason) {
            $message->line('Reason: ' . $this->reason);
        }

        return $message
                    ->line('Thank you for understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'thread_id' => $this->comment->thread_id,
            'action' => $this->action,
            'reason' => $this->reason,
        ];
    }
}
