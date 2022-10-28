<?php

namespace App\Http\Requests\Application\Estimate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Update extends FormRequest
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
            'estimate_number' => ['required', Rule::unique('estimates')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })->ignore($this->route('estimate'))],
            'estimate_number' => 'required|unique:estimates,estimate_number,' . $this->route('estimate'),
            'estimate_date' => 'required|date',
            'expiry_date' => 'required|date',
            'reference_number' => 'nullable|string',
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'sub_total' => 'required|integer',
            'grand_total' => 'required|integer',
            'notes' => 'nullable|string',
            'private_notes' => 'nullable|string',
            'template_id' => 'required|integer|exists:estimate_templates,id',

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
            'estimate_number.unique' => __('messages.estimate_exists'),
            'product.required' => __('messages.please_select_a_product'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'estimate_number' => $this->estimate_prefix . '-' . sprintf('%06d', intval($this->estimate_number)),
        ]);
    }
}
