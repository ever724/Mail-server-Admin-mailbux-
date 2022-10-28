<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'display_name' => $this->display_name,
            'contact_name' => $this->contact_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'website' => $this->website,
            'currency' => $this->currency_code,
            'vat_number' => $this->vat_number,
            'invoice_due_amount' => $this->invoice_due_amount,
            'billing_address' => AddressResource::make($this->billing),
            'shipping_address' => AddressResource::make($this->shipping),
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'fields' => CustomFieldResource::collection($this->whenLoaded('fields')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
