<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NoEmoji implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return !preg_match('/[\x{1F600}-\x{1F64F}' . // Emoticons
            '\x{1F300}-\x{1F5FF}' . // Symbols & Pictographs
            '\x{1F680}-\x{1F6FF}' . // Transport & Map
            '\x{1F700}-\x{1F77F}' . // Alchemical Symbols
            '\x{1F780}-\x{1F7FF}' . // Geometric Shapes Extended
            '\x{1F800}-\x{1F8FF}' . // Supplemental Arrows-C
            ']/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must not contain emojis.';
    }
}
