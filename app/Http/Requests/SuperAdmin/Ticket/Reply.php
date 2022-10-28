<?php

namespace App\Http\Requests\SuperAdmin\Ticket;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class Reply extends FormRequest
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
        $rules = [
            'body' => ['required', 'string', 'min:16'],
            'files' => ['array'],
            'files.*' => ['file', 'max:8192', 'min:16'],
        ];

        if ($this->user() == null) {
            $rules['email'] = ['required', 'email'];
        }

        return $rules;
    }

    /**
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->getMessageBag()->getMessages(), 422)
        );
    }
}
