<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Mark notification as read.
     */
    public function markAsRead(): void
    {
        if ($this->isUnread()) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->forceFill(['read_at' => null])->save();
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return !$this->isUnread();
    }

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function user()
    {
        return $this->notifiable;
    }

    /**
     * Scope to only unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope to only read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope to notifications of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('data->type', $type);
    }

    /**
     * Scope to recent notifications.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Get notification title from data.
     */
    public function getTitle(): ?string
    {
        return $this->data['document_title']
            ?? $this->data['incident_title']
            ?? $this->data['title']
            ?? null;
    }

    /**
     * Get notification action URL from data.
     */
    public function getActionUrl(): ?string
    {
        return $this->data['action_url'] ?? null;
    }
}
