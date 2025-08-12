<?php

namespace Tests\Unit\Rules;

use App\Rules\CpfRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class CpfRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_cpf_rule_with_valid_values(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        $validCpfs = [
            '36224898120', '35156285686', '32524966070',
            '298.906.171-10', '784.557.535-60', '364.991.292-91',
            $faker->cpf(), $faker->cpf(), $faker->cpf(),
        ];

        foreach ($validCpfs as $cpf) {
            $this->assertTrue(
                Validator::make(
                    ['cpf' => $cpf],
                    ['cpf' => new CpfRule]
                )->passes()
            );
        }
    }

    public function test_cpf_rule_with_invalid_values(): void
    {
        $faker = \Faker\Factory::create('pt_BR');

        $invalidCpfs = [
            '0', '0123', '00000000000', '01234567891', 'a00.000.000-01',
            '000.000.000-a1', '298.906.171-01', '784.557.535-06',
            '364.991.292-19', $faker->cpf() . 1, Str::random(11),
        ];

        foreach ($invalidCpfs as $cpf) {
            $this->assertFalse(
                Validator::make(
                    ['cpf' => $cpf],
                    ['cpf' => new CpfRule]
                )->passes()
            );
        }
    }
}
