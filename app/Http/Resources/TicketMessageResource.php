<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="TicketMessage",
 *     @OA\Property(property="message_body", example="Example message body"),
 *     @OA\Property(property="sent_at", example="2022-01-20 10:20:22"),
 *     @OA\Property(property="sender", type="object",
 *         @OA\Property(property="name", example="John Doe"),
 *         @OA\Property(property="email", example="johndoe@example.com"),
 *     ),
 *     @OA\Property(property="attachments", type="array",
 *         @OA\Items(ref="#/components/schemas/TicketMessageAttachment")
 *     ),
 *     @OA\Property(property="is_read_by_client", type="bool", example=true),
 * )
 */
class TicketMessageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'message_body' => $this->body,
            'sent_at' => $this->created_at->format('Y-m-d H:i:s'),
            'sender' => [
                'name' => $this->sender_name,
                'email' => $this->sender_email,
            ],
            'attachments' => TicketMessageAttachmentResource::collection($this->whenLoaded('attachments')),
            'is_read_by_client' => (bool) $this->is_read_by_client,
        ];
    }
}
