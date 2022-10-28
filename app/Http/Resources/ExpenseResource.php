<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'expense_date' => $this->expense_date,
            'formatted_expense_date' => $this->formatted_expense_date,
            'notes' => $this->notes,
            'attachment_receipt' => $this->attachment_receipt,
            'is_recurring' => $this->is_recurring,
            'cycle' => $this->cycle,
            'next_recurring_at' => $this->next_recurring_at,
            'amount' => $this->amount,
            'currency' => $this->currency_code,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'vendor' => VendorResource::make($this->whenLoaded('vendor')),
            'category' => ExpenseCategoryResource::make($this->whenLoaded('category')),
            'fields' => CustomFieldResource::collection($this->whenLoaded('fields')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
