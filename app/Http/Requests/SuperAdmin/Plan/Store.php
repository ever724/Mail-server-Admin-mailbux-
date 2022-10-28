<?php

namespace App\Http\Requests\SuperAdmin\Plan;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
            'annual_price' => 'required|integer',
            'monthly_price' => 'required|integer',
            'annual_sales_price' => 'nullable|integer',
            'monthly_sales_price' => 'nullable|integer',
            'trial_period' => 'sometimes|integer|max:100000',
            'invoice_period' => 'sometimes|integer|max:100000',
            'grace_period' => 'sometimes|integer|max:100000',
            'order' => 'integer',
            'paddle_annual_id' => 'required|integer',
            'paddle_monthly_id' => 'required|integer',
            'mailbux_settings' => ['array'],
            'mailbux_settings.*' => ['integer', 'required'],
            'features.*.label' => ['required', 'string', 'max:50'],
            'features.*.value' => ['required', 'string', 'max:10'],
            'features.*.is_displayed' => ['required', 'boolean'],
            'features.*.order' => ['distinct', 'required', 'integer'],
        ];
    }
}
