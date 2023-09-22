<?php

namespace App\Traits;

trait RandomNumberGenerator
{
    public function generateRandomNumber(int $numberOfDigits): string
    {
        $number = '';

        for ($i = 0; $i < $numberOfDigits; $i++) {
            $number .= mt_rand(0, 9);
        }

        return $number;
    }
}
