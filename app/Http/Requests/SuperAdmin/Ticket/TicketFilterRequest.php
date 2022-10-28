<?php

namespace App\Http\Requests\SuperAdmin\Ticket;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class TicketFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'include_closed' => ['nullable', 'bool'],
            'category' => ['nullable', 'string'],
            'subject' => ['nullable', 'string'],
            'create_date.from' => ['nullable', 'date_format:Y-m-d', 'required_with:create_date.until', 'before_or_equal:create_date.until'],
            'create_date.until' => ['nullable', 'required_with:create_date.from', 'date_format:Y-m-d', 'after_or_equal:create_date.from'],
            'last_update.from' => ['nullable', 'date_format:Y-m-d', 'required_with:last_update.until', 'before_or_equal:last_update.until'],
            'last_update.until' => ['nullable', 'required_with:last_update.from', 'date_format:Y-m-d', 'after_or_equal:last_update.from'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->getMessageBag()->getMessages(), 422)
        );
    }
}
