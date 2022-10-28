<?php

namespace App\Http\Requests\Application\Product;

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
            'name' => 'required|string|max:190',
            'unit_id' => ['required', 'integer', Rule::exists('product_units', 'id')->where(function ($query) {
                return $query->where('company_id', request()->user()->currentCompany()->id)->orWhere('company_id', null);
            })],
            'price' => 'required|integer',
            'description' => 'nullable|string|max:500',
        ];
    }
}
