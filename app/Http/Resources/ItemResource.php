<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'description' => $this->description,
            'company_id' => $this->company_id,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount_type' => $this->discount_type,
            'discount_val' => $this->discount_val,
            'total' => $this->total,
            'currency' => $this->currency_code,
            'product' => ProductResource::make($this->whenLoaded('product')),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
