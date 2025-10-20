<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DocumentRouting extends Model
{
    use LogsActivity;

    protected $fillable = [
        'document_id',
        'from_user_id',
        'to_user_id',
        'message',
        'status',
        'is_confidential',
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'to_user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Document routing {$eventName}")
            ->useLogName('document_routing');
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function markAsOpened(): void
    {
        if ($this->status === 'sent') {
            $this->update(['status' => 'opened']);

            activity()
                ->performedOn($this->document)
                ->causedBy(auth()->user())
                ->withProperties([
                    'routing_id' => $this->id,
                    'sender_id' => $this->from_user_id,
                ])
                ->event('viewed')
                ->log('Document opened by recipient');
        }
    }

    public function markAsForwarded(): void
    {
        $this->update(['status' => 'forwarded']);

        activity()
            ->performedOn($this->document)
            ->causedBy(auth()->user())
            ->withProperties([
                'routing_id' => $this->id,
                'original_recipient_id' => $this->to_user_id,
            ])
            ->event('forwarded')
            ->log('Document forwarded');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeOpened($query)
    {
        return $query->where('status', 'opened');
    }

    public function scopeForwarded($query)
    {
        return $query->where('status', 'forwarded');
    }

    public function scopeToUser($query, $userId)
    {
        return $query->where('to_user_id', $userId);
    }

    public function scopeFromUser($query, $userId)
    {
        return $query->where('from_user_id', $userId);
    }

    public function scopeConfidential($query)
    {
        return $query->where('is_confidential', true);
    }
}
