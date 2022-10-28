<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteRefundResource extends JsonResource
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
            'id' => $this->id,
            'credit_note' => CreditNoteResource::make($this->whenLoaded('credit_note')),
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_method')),
            'refund_date' => $this->refund_date,
            'formatted_refund_date' => $this->formatted_refund_date,
            'amount' => $this->amount,
            'currency' => $this->currency_code,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
