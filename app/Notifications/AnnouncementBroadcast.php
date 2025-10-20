<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnouncementBroadcast extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $title,
        public string $message,
        public User $sender,
        public string $scope,
        public ?int $departmentId = null
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
        $scopeText = $this->scope === 'all_departments' ? 'Organization-wide' : 'Department';

        return (new MailMessage)
            ->subject('[' . $scopeText . ' Announcement] ' . $this->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('**' . $this->title . '**')
            ->line($this->message)
            ->line('From: ' . $this->sender->first_name . ' ' . $this->sender->last_name)
            ->when($this->scope === 'all_departments', function ($mail) {
                return $mail->line('This is an organization-wide announcement.');
            })
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
            'type' => 'announcement_broadcast',
            'title' => $this->title,
            'message' => $this->message,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->first_name . ' ' . $this->sender->last_name,
            'scope' => $this->scope,
            'department_id' => $this->departmentId,
        ];
    }
}
