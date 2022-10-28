<?php

namespace App\Http\Requests\SuperAdmin\Client;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
            'name' => ['required', 'string', 'min:2'],
            'password' => ['string', 'nullable', 'min:12'],
            'password_repeat' => ['nullable', 'same:password'],
            'organization' => ['nullable', 'string'],
            'recovery_email' => ['required', 'email'],
            'api_access' => ['required', 'boolean'],
            'enabled' => ['required', 'boolean'],
        ];
    }
}
