<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name' => $this->name,
            'owner_id' => $this->owner_id,
            'vat_number' => $this->vat_number,
            'currency' => $this->currency->short_code,
            'avatar' => $this->avatar,
            'billing_address' => AddressResource::make($this->whenLoaded('billing')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
