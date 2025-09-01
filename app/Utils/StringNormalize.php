<?php

namespace App\Utils;

use Normalizer;

class StringNormalize
{
    private function __construct(private ?string $value)
    {
        $this->value = $value ?? '';
    }

    /**
     * Returns a new instance of the StringNormalize class, given a string value.
     */
    public static function for(?string $value): StringNormalize
    {
        return new self($value);
    }

    /**
     * Returns the normalized string value.
     */
    public function get(): string
    {
        return $this->value;
    }

    /**
     * Removes accents, extra white spaces, and converts the string to lowercase.
     */
    public function defaultNormalize(): self
    {
        return $this->removeAccents()
            ->removeExtraWhiteSpaces()
            ->toLowerCase();
    }

    /**
     * Removes everything that is not a digit or whitespace from the given string.
     */
    public function keepOnlyDigits(): self
    {
        $this->value = preg_replace('/[^\d\s]/', '', $this->value);

        return $this;
    }

    /**
     * Removes everything that is not a letter or whitespace from the given string.
     */
    public function keepOnlyLetters(): self
    {
        $this->value = preg_replace('/[^A-Za-z\s]/', '', $this->value);

        return $this;
    }

    /**
     * Normalize memory size product description by converting different representations.
     */
    public function normalizeMemorySize(): self
    {
        $this->value = preg_replace('/(\d+)(\s*)(Gb|GB|gb|gB)/', '$1gb', $this->value);

        return $this;
    }

    /**
     * Removes accents from the given string using the decomposition technique.
     */
    public function removeAccents(): self
    {
        $decomposedValue = Normalizer::normalize($this->value, Normalizer::FORM_D);
        $this->value = preg_replace('/\p{Mn}/u', '', $decomposedValue);

        return $this;
    }

    /**
     * Removes non-alphanumeric characters from the given string.
     */
    public function removeNonAlphanumeric(): self
    {
        $this->value = preg_replace('/[^A-Za-z\d\s]/', '', $this->value);

        return $this;
    }

    /**
     * Removes extra white spaces from the given string.
     */
    public function removeExtraWhiteSpaces(): self
    {
        $this->value = trim(preg_replace('/\s+/', ' ', $this->value));

        return $this;
    }

    /**
     * Convert the given string to lowercase.
     */
    public function toLowerCase(): self
    {
        $this->value = strtolower($this->value);

        return $this;
    }
}
