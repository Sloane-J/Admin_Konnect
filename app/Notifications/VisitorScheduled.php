<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VisitorScheduled extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public VisitorVisit $visit
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
        $visitorName = $this->visit->visitor_first_name . ' ' . $this->visit->visitor_last_name;
        $visitDate = \Carbon\Carbon::parse($this->visit->visit_date)->format('l, F j, Y');

        return (new MailMessage)
            ->subject('Visitor Scheduled: ' . $visitorName)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('A visitor has been scheduled to meet with you.')
            ->line('**Visitor:** ' . $visitorName)
            ->line('**Company:** ' . ($this->visit->visitor_company ?? 'Not specified'))
            ->line('**Date:** ' . $visitDate)
            ->line('**Purpose:** ' . $this->visit->purpose)
            ->when($this->visit->notes, function ($mail) {
                return $mail->line('**Notes:** ' . $this->visit->notes);
            })
            ->action('View Visit Details', route('visitors.show', $this->visit->id))
            ->line('Please ensure you are available for this visit.')
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
            'type' => 'visitor_scheduled',
            'visit_id' => $this->visit->id,
            'visitor_name' => $this->visit->visitor_first_name . ' ' . $this->visit->visitor_last_name,
            'visitor_company' => $this->visit->visitor_company,
            'visit_date' => $this->visit->visit_date,
            'purpose' => $this->visit->purpose,
            'action_url' => route('visitors.show', $this->visit->id),
        ];
    }
}
