<?php

namespace App\Traits;

trait StringNormalizer
{
    /**
     * Remove everything that is not a digit from the given string.
     */
    protected static function extractOnlyDigits(string $value): string
    {
        return preg_replace('/[^\d\s]/', '', $value);
    }

    /**
     * Remove extra white spaces from the given string.
     */
    protected static function removeExtraWhiteSpaces(string $value): string
    {
        return trim(preg_replace('/\s+/', ' ', $value));
    }
}
