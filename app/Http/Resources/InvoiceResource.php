<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'invoice_date' => $this->invoice_date,
            'formatted_invoice_date' => $this->formatted_invoice_date,
            'due_date' => $this->due_date,
            'formatted_due_date' => $this->formatted_due_date,
            'invoice_number' => $this->invoice_number,
            'reference_number' => $this->reference_number,
            'tax_per_item' => $this->tax_per_item,
            'discount_per_item' => $this->discount_per_item,
            'status' => $this->status,
            'paid_status' => $this->paid_status,
            'notes' => $this->notes,
            'private_notes' => $this->private_notes,
            'discount_type' => $this->discount_type,
            'discount_val' => $this->discount_val,
            'sub_total' => $this->sub_total,
            'total' => $this->total,
            'due_amount' => $this->due_amount,
            'currency' => $this->currency_code,
            'is_archived' => $this->is_archived,
            'template_id' => $this->template_id,
            'sent' => $this->sent,
            'viewed' => $this->viewed,
            'is_recurring' => $this->is_recurring,
            'cycle' => $this->cycle,
            'next_recurring_at' => $this->next_recurring_at,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'fields' => CustomFieldResource::collection($this->whenLoaded('fields')),
            'items' => ItemResource::collection($this->whenLoaded('items')),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
