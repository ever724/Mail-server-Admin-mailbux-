<?php

namespace App\Http\Requests\Application\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $max_lenght = is_array($this->product) ? count($this->product) : 0;

        return [
            'invoice_number' => ['required', Rule::unique('invoices')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'invoice_date' => 'required|date',
            'due_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'sub_total' => 'required|integer',
            'grand_total' => 'required|integer',
            'notes' => 'nullable|string',
            'private_notes' => 'nullable|string',
            'template_id' => 'required|integer|exists:invoice_templates,id',
            'is_recurring' => 'required|integer',
            'cycle' => 'required|integer',

            'total_discount' => 'sometimes|integer|min:0',
            'total_taxes' => 'sometimes|array|min:0',

            'product' => 'required|array|max:' . $max_lenght,
            'product.*' => 'required',

            'quantity' => 'required|array|max:' . $max_lenght,
            'quantity.*' => 'required|integer|min:0',

            'price' => 'required|array|max:' . $max_lenght,
            'price.*' => 'required',

            'total' => 'required|array|max:' . $max_lenght,
            'total.*' => 'required',

            'taxes' => 'sometimes|required|array|max:' . $max_lenght,
            'taxes.*' => 'sometimes|required|array',

            'discount' => 'sometimes|required|array|max:' . $max_lenght,
            'discount.*' => 'sometimes|required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'invoice_number.unique' => __('messages.invoice_exists'),
            'product.required' => __('messages.please_select_a_product'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'invoice_number' => $this->invoice_prefix . '-' . sprintf('%06d', intval($this->invoice_number)),
        ]);
    }
}
