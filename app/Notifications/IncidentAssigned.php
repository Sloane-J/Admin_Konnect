<?php

namespace App\Notifications;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncidentAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Incident $incident,
        public User $assignedBy
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
            ->subject('Incident Assigned: ' . $this->incident->title)
            ->greeting('Hello ' . $notifiable->first_name . ',')
            ->line('An incident has been assigned to you by ' . $this->assignedBy->first_name . ' ' . $this->assignedBy->last_name . '.')
            ->line('**Incident:** ' . $this->incident->title)
            ->line('**Type:** ' . ucfirst(str_replace('_', ' ', $this->incident->incident_type)))
            ->line('**Location:** ' . ($this->incident->location ?? 'Not specified'))
            ->line('**Description:** ' . $this->incident->description)
            ->action('View Incident', route('incidents.show', $this->incident->id))
            ->line('Please address this incident promptly.')
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
            'type' => 'incident_assigned',
            'incident_id' => $this->incident->id,
            'incident_title' => $this->incident->title,
            'incident_type' => $this->incident->incident_type,
            'assigned_by_id' => $this->assignedBy->id,
            'assigned_by_name' => $this->assignedBy->first_name . ' ' . $this->assignedBy->last_name,
            'action_url' => route('incidents.show', $this->incident->id),
        ];
    }
}
