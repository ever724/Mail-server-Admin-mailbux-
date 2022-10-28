<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property string $creator_email
 * @property string $creator_name
 * @property string $subject
 * @property bool $receive_notifications
 * @property string $category
 * @property Carbon $closed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class SupportTicket extends Model
{
    use HasTimestamps;
    use SoftDeletes;

    private $_first_message;

    const STATUS_CLOSED = 'closed';
    const STATUS_OPEN = 'open';

    protected $dates = [
        'closed_at',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'creator_email',
        'creator_name',
        'subject',
        'receive_notifications',
        'category',
        'closed_at',
    ];

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id', 'id')
            ->orderBy('created_at', 'ASC');
    }

    /**
     * @return null|HasMany|Model|object
     */
    public function getFirstMessageAttribute()
    {
        if (isset($this->_first_message)) {
            return $this->_first_message;
        }

        $this->_first_message = $this->messages()->oldest()->first() ?? null;

        return $this->_first_message;
    }

    /**
     * @return string
     */
    public function getStatusAttribute(): string
    {
        if (!is_null($this->closed_at)) {
            return self::STATUS_CLOSED;
        }

        return self::STATUS_OPEN;
    }

    public function getUnreadMessagesAttribute(): int
    {
        return $this->messages()->where('is_read_by_admin', false)->count();
    }

    public function reopen()
    {
        $this->closed_at = null;
    }
}
