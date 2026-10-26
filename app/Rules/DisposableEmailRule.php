<?php

namespace App\Rules;

use App\Services\DisposableEmailService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DisposableEmailRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (DisposableEmailService::isDisposable($value)) {
            $fail('Please use a permanent email address from an established provider. Temporary or disposable email services are not permitted.');
        }
    }
}
