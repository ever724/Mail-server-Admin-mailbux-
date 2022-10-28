<?php

namespace App\Http\Requests\SuperAdmin\Ticket;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

final class CreateTicket extends FormRequest
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
            'ticket.subject' => ['required', 'string'],
            'ticket.receive_notifications' => ['required', 'bool'],
            'ticket.category' => ['string', 'nullable'],
            'message.body' => ['required', 'string', 'min:8'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json($validator->getMessageBag()->getMessages(), 422)
        );
    }
}
