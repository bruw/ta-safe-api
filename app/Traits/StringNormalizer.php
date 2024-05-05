<?php

namespace App\Traits;

trait StringNormalizer
{
    /**
     * Remove everything that is not a digit from the given string.
     */
    protected function extractOnlyDigits(?string $value): string
    {
        return is_null($value) ? '' : preg_replace('/[^\d\s]/', '', $value);
    }

    /**
     * Remove extra white spaces from the given string.
     */
    protected function removeExtraWhiteSpaces(?string $value): string
    {
        return is_null($value) ? '' : trim(preg_replace('/\s+/', ' ', $value));
    }
}
