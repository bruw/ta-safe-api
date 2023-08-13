<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CpfRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $cpf = preg_replace('/\D/', '', $value);

        if (strlen($cpf) != 11) {
            $fail(trans('validation.custom.cpf.invalid_format'));
        } else {
            $validateCheckDigits = true;

            if (preg_match('/(\d)\1{10}/', $cpf)) {
                $fail(trans('validation.custom.cpf.repeated_sequence'));
                $validateCheckDigits = false;
            }

            if ($validateCheckDigits) {
                for ($t = 9; $t < 11 && $validateCheckDigits; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }

                    $d = (($d * 10) % 11) % 10;

                    if ($cpf[$c] != $d) {
                        $fail(trans('validation.custom.cpf.invalid_check_digits'));
                        $validateCheckDigits = false;
                    }
                }
            }
        }
    }
}
