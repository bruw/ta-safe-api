<?php

namespace App\Rules;

use App\Models\DeviceSharingToken;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExpiredDeviceSharingTokenRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $token = DeviceSharingToken::where('token', $value)->first();

        if (! $token) {
            $fail(trans('validation.custom.token.exists'));

            return;
        }

        if ($token->isExpired()) {
            $fail(trans('validation.custom.token.expired'));
        }
    }
}
