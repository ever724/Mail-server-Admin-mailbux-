<?php

namespace App\Http\Requests\SuperAdmin\Client;

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
            'name' => ['required', 'string', 'min:2'],
            'username' => ['required', 'string'],
            'domain' => ['required', 'string'], // To validate allowed domains when implemented
            'password' => ['required', 'min:12'],
            'password_repeat' => ['required', 'same:password'],
            'organization' => ['nullable', 'string'],
            'recovery_email' => ['required', 'email'],
            'api_access' => ['required', 'boolean'],
            'enabled' => ['required', 'boolean'],
        ];
    }
}
