<?php

namespace App\Http\Requests\Application\Expense;

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
        return [
            'expense_category_id' => ['required', Rule::exists('expense_categories', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'vendor_id' => ['sometimes', Rule::exists('vendors', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id);
            })],
            'amount' => 'required|integer',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
            'is_recurring' => 'required|integer',
            'cycle' => 'required|integer',
        ];
    }
}
