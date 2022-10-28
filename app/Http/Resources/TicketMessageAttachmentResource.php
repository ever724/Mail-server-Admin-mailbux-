<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TicketMessageAttachment",
 *     @OA\Property(property="name", example="attachment.jpg"),
 *     @OA\Property(property="size", example=1024, type="int"),
 *     @OA\Property(property="link", type="string", example="https://api.example/link/to/download"),
 * )
 */
class TicketMessageAttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->file_name,
            'size' => $this->size,
            'link' => route('api.support-tickets.download-attachment', [$this->message->ticket, $this->id]),
        ];
    }
}
