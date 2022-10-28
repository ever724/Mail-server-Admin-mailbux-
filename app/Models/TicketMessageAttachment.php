<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketMessageAttachment extends Model
{
    use HasTimestamps;

    const DISK = 'local';

    protected $fillable = [
        'message_id',
        'file_name',
        'size',
        'storage_disk',
        'storage_path',
    ];

    /**
     * @return BelongsTo
     */
    public function message(): BelongsTo
    {
        return $this->belongsTo(TicketMessage::class);
    }
}
