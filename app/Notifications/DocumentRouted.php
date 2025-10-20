<?php

namespace App\Notifications;

use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRouted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Document $document,
        public User $sender,
        public ?string $message = null
    ) {}

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
        return (new MailMessage)
            ->subject('New Document Sent: ' . $this->document->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line($this->sender->first_name . ' ' . $this->sender->last_name . ' has sent a document to you.')
            ->line('**Document:** ' . $this->document->title)
            ->when($this->message, function ($mail) {
                return $mail->line('**Message:** ' . $this->message);
            })
            ->action('View Document', route('documents.show', $this->document->id))
            ->line('Please review and take appropriate action.')
            ->salutation('Regards, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'document_routed',
            'document_id' => $this->document->id,
            'document_title' => $this->document->title,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->first_name . ' ' . $this->sender->last_name,
            'message' => $this->message,
            'action_url' => route('documents.show', $this->document->id),
        ];
    }
}
