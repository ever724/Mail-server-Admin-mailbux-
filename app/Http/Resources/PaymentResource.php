<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'invoice_id' => $this->invoice_id,
            'credit_note_id' => $this->credit_note_id,
            'transaction_reference' => $this->transaction_reference,
            'payment_date' => $this->payment_date,
            'formatted_payment_date' => $this->formatted_payment_date,
            'payment_number' => $this->payment_number,
            'amount' => $this->amount,
            'currency' => $this->currency_code,
            'notes' => $this->notes,
            'private_notes' => $this->private_notes,
            'is_archived' => $this->is_archived,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'fields' => CustomFieldResource::collection($this->whenLoaded('fields')),
            'payment_type' => PaymentTypeResource::make($this->whenLoaded('payment_method')),
            'invoice' => InvoiceResource::make($this->whenLoaded('invoice')),
            'credit_note' => CreditNoteResource::make($this->whenLoaded('credit_note')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
