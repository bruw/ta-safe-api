<?php

namespace App\Traits;

use Exception;

trait StringMasks
{
    public function addAsteriskMaskForImei(string $imei): string
    {
        if (strlen($imei) != 15)
            throw new Exception('The imei must have 15 digits!');

        $asterisks = str_repeat('*', 9);
        return substr_replace($imei, $asterisks, 3, 9);
    }

    public function addAsteriskMaskForCpf(string $cpf): string
    {
        if (strlen($cpf) != 14)
            throw new Exception('The cpf must have 14 digits!');

        $asterisks = str_repeat('*', 10);
        return substr_replace($cpf, $asterisks, 2, 10);
    }

    public function addAsteriskMaskForPhone(string $phone): string
    {
        if (strlen($phone) != 15)
            throw new Exception('The phone must have 15 characters!');

        $asterisks = str_repeat('*', 8);
        return substr_replace($phone, $asterisks, 4, 8);
    }
}
