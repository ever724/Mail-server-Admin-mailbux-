<?php

namespace App\Http\Requests\Application\Payment;

use App\Models\CreditNote;
use App\Models\PaymentMethod;
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
        return [
            'payment_number' => ['required', Rule::unique('payments')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'payment_date' => 'required|date',
            'customer_id' => ['required', Rule::exists('customers', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'credit_note_id' => ['nullable', Rule::exists('credit_notes', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'invoice_id' => ['nullable', Rule::exists('invoices', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'payment_method_id' => ['required', 'integer', function ($attribute, $value, $fail) {
                if (
                    !PaymentMethod::findByCompany(request()->user()->currentCompany()->id)->where('id', $value)->exists() ||
                    CreditNote::findByCompany(request()->user()->currentCompany()->id)->where('id', $value)->exists()
                ) {
                    return $fail("The provided {$attribute} is not valid.");
                }
            }],
            'amount' => 'required|integer',
            'notes' => 'nullable|string',
            'private_notes' => 'nullable|string',
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
            'payment_number.unique' => __('messages.payment_exists'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'payment_number' => $this->payment_prefix . '-' . sprintf('%06d', intval($this->payment_number)),
        ]);
    }
}
