<?php

namespace App\Traits;

use Normalizer;

trait StringNormalizer
{
    /**
     * Normalizes the given string by removing accents, extra white spaces,
     * and converting it to lowercase.
     */
    protected function basicNormalize(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $unaccented = $this->removeAccents($value);
        $whitoutExtraWhiteSpaces = $this->removeExtraWhiteSpaces($unaccented);
        $result = strtolower($whitoutExtraWhiteSpaces);

        return $result;
    }

    /**
     * Remove everything that is not a digit from the given string.
     */
    protected function extractOnlyDigits(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $onlyDigits = preg_replace('/[^\d\s]/', '', $value);

        return $this->removeExtraWhiteSpaces($onlyDigits);
    }

    /**
     * Removes everything that is not a letter in the given string.
     */
    protected function extractOnlyLetters(?string $value): string
    {
        if (is_null($value)) {
            return '';
        }

        $unaccented = $this->removeAccents($value);
        $onlyLetters = strtolower(preg_replace('/[^A-Za-z\s]/', '', $unaccented));

        return $this->removeExtraWhiteSpaces($onlyLetters);
    }

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
     * Removes accents from a string using the decomposition technique.
     */
    protected function removeAccents(?string $value): string
    {
        $decomposedValue = Normalizer::normalize($value, Normalizer::FORM_D);

        return preg_replace('/\p{Mn}/u', '', $decomposedValue);
    }

    /**
     * Remove all characters from the given string that are not alphanumeric.
     */
    protected function removeNonAlphanumeric(?string $value): string
    {
        return preg_replace('/[^A-Za-z\d\s]/', '', $value);
    }
}
