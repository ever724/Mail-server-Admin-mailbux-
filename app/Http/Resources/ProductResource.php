<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'currency' => $this->currency_code,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'unit' => ProductUnitResource::make($this->whenLoaded('unit')),
            'fields' => CustomFieldResource::collection($this->whenLoaded('fields')),
            'taxes' => TaxResource::collection($this->whenLoaded('taxes')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
