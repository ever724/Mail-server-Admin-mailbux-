<?php

namespace App\Http\Requests\Rules;

use App\Services\Language\Drivers\Translation;
use Illuminate\Contracts\Validation\Rule;

class LanguageNotExists implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $translation = app()->make(Translation::class);

        return !$translation->languageExists($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('messages.language_exists');
    }
}
