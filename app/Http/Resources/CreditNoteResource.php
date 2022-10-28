<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteResource extends JsonResource
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
            'uid' => $this->uid,
            'credit_note_date' => $this->credit_note_date,
            'formatted_credit_note_date' => $this->formatted_credit_note_date,
            'credit_note_number' => $this->credit_note_number,
            'reference_number' => $this->reference_number,
            'tax_per_item' => $this->tax_per_item,
            'discount_per_item' => $this->discount_per_item,
            'status' => $this->status,
            'notes' => $this->notes,
            'private_notes' => $this->private_notes,
            'discount_type' => $this->discount_type,
            'discount_val' => $this->discount_val,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'remaining_balance' => $this->remaining_balance,
            'currency' => $this->currency_code,
            'is_archived' => $this->is_archived,
            'template_id' => $this->template_id,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'fields' => CustomFieldResource::collection($this->whenLoaded('fields')),
            'items' => ItemResource::collection($this->whenLoaded('items')),
            'refunds' => CreditNoteRefundResource::collection($this->whenLoaded('refunds')),
            'applied_payments' => PaymentResource::collection($this->whenLoaded('applied_payments')),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
