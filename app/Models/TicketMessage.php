<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property SupportTicket $ticket
 * @property ?User $sender
 * @property string $body
 * @property string $sender_email
 * @property string $sender_name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class TicketMessage extends Model
{
    use HasTimestamps;

    /**
     * @var string[]
     */
    protected $fillable = [
        'ticket_id',
        'sender_id',
        'body',
        'is_read_by_admin',
        'is_read_by_client',
    ];

    /**
     * @return BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id', 'id');
    }

    /**
     * @return string
     */
    public function getSenderEmailAttribute(): string
    {
        if ($this->sender) {
            return $this->sender->email;
        }

        return $this->ticket->creator_email;
    }

    /**
     * @return string
     */
    public function getSenderNameAttribute(): string
    {
        if ($this->sender) {
            return $this->sender->full_name;
        }

        return $this->ticket->creator_name;
    }

    /**
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TicketMessageAttachment::class, 'message_id', 'id');
    }
}
