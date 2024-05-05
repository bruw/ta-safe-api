<?php

namespace App\Traits;

trait StringNormalizer
{
    /**
     * Remove extra white spaces from the given string.
     */
    protected function removeExtraWhiteSpaces(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        return trim(preg_replace('/\s+/', ' ', $value));
    }

    /**
     * Remove everything that is not a digit from the given string.
     */
    protected function extractOnlyDigits(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $extracted = preg_replace('/[^\d\s]/', '', $value);

        return $this->removeExtraWhiteSpaces($extracted);
    }
}
