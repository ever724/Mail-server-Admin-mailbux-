<?php

namespace App\Http\Resources;

use App\Models\SupportTicket;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SupportTicket",
 *     title="SupportTicket",
 *     @OA\Property(property="id", type="int", example=1),
 *     @OA\Property(property="creator", type="object",
 *         @OA\Property(property="name", example="John Doe"),
 *         @OA\Property(property="email", example="johndoe@example.com"),
 *     ),
 *     @OA\Property(property="category", type="string", example="test"),
 *     @OA\Property(property="subject", type="string", example="Help"),
 *     @OA\Property(property="created_at", type="string", example="2022-01-20 10:10:11"),
 *     @OA\Property(property="updated_at", type="string", example="2022-01-20 10:10:11"),
 *     @OA\Property(property="is_closed", type="bool", example=false),
 *     @OA\Property(property="closed_at", type="string", example="2022-01-20 10:10:11"),
 *     @OA\Property(property="messages", type="array",
 *         @OA\Items(ref="#/components/schemas/TicketMessage")
 *     ),
 * )
 */
class SupportTicketResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'creator' => [
                'name' => $this->creator_name,
                'email' => $this->creator_email,
            ],
            'category' => $this->category,
            'subject' => $this->subject,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'is_closed' => $this->status == SupportTicket::STATUS_CLOSED,
            'closed_at' => $this->closed_at ? $this->closed_at->format('Y-m-d H:i:s') : null,
            'messages' => TicketMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
