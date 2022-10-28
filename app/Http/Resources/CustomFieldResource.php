<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomFieldResource extends JsonResource
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
            'model_type' => $this->model_type,
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'placeholder' => $this->placeholder,
            'is_required' => $this->is_required,
            'default_answer' => $this->default_answer,
            'options' => $this->options,
            'order' => $this->order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
