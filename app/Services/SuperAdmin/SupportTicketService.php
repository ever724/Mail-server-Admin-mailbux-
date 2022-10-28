<?php

namespace App\Services\SuperAdmin;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\TicketMessageAttachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class SupportTicketService
{
    const ATTACHMENTS_DISK = TicketMessageAttachment::DISK;

    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $storage;

    public function __construct()
    {
        $this->storage = Storage::disk(self::ATTACHMENTS_DISK);
    }

    /**
     * @param SupportTicket $ticket
     * @param array         $data
     * @param null|int      $userId
     *
     * @throws \Exception
     *
     * @return SupportTicket
     */
    public function storeReply(SupportTicket $ticket, array $data, ?int $userId = null): SupportTicket
    {
        $message = new TicketMessage();

        if ($userId) {
            $message->sender_id = $userId;
        } else {
            if (Arr::get($data, 'email') !== $ticket->creator_email) {
                throw new \Exception('This user cannot reply to this ticket');
            }
            $ticket->reopen();
        }

        $message->ticket_id = $ticket->id;
        $message->body = Arr::get($data, 'body');
        $message->save();

        foreach (($data['files'] ?? []) as $file) {
            $this->storeFile($message, $file);
        }

        $ticket->touch();
        $ticket->save();

        return $ticket;
    }

    /**
     * @param TicketMessage $message
     * @param UploadedFile  $file
     */
    private function storeFile(TicketMessage $message, UploadedFile $file)
    {
        $filename = $file->getClientOriginalName();
        $storagePath = sprintf('ticket-attachments/%d-%s', time(), $filename);
        $size = $file->getSize();

        $this->storage->put($storagePath, $file->getContent());

        $message->attachments()->create([
            'file_name' => $filename,
            'size' => $size,
            'storage_disk' => self::ATTACHMENTS_DISK,
            'storage_path' => $storagePath,
        ]);
    }
}
