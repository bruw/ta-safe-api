<?php

namespace Lib\Strings;

class StringHelper
{
    /**
     * Capitalize a string. 
     */
    public static function capitalize(string $inputString): string
    {
        $exceptions = ['e', 'de', 'da', 'do', 'dos', 'das'];

        $inputString = mb_strtolower($inputString, 'UTF-8');
        $words = explode(' ', $inputString);

        foreach ($words as &$word) {
            if (!in_array($word, $exceptions) || key($words) == 0) {
                $word = mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
            }

            next($words);
        }

        return trim(preg_replace('/\s+/', ' ', implode(' ', $words)));
    }
}
