<?php

namespace Tests\Unit\Lib\StringHelper;

use Lib\Strings\StringHelper;
use tests\TestCase;

class CapitalizeTest extends TestCase
{
    public function test_must_correctly_capitalize_the_names_according_to_the_expected_results(): void
    {
        $names = [
            'maria de amorim dos santos e oliveira',
            'joão da silva',
            'guilherme dos santos',
            'maria das graças',
            'andre'
        ];

        $expectedResults = [
            'Maria de Amorim dos Santos e Oliveira',
            'João da Silva',
            'Guilherme dos Santos',
            'Maria das Graças',
            'Andre'
        ];

        $results = [];

        foreach ($names as $name) {
            array_push($results, StringHelper::capitalize($name));
        }

        $this->assertEquals($results, $expectedResults);
    }

    public function test_must_correctly_capitalize_names_with_accents_according_to_the_expected_results(): void
    {
        $names = [
            'ícaro da silva',
            'cássia dos santos e silva',
            'verônica das graças e paula',
            'joão carlos de matos',
            'ângela da costa e figueiredo'
        ];

        $expectedResults = [
            'Ícaro da Silva',
            'Cássia dos Santos e Silva',
            'Verônica das Graças e Paula',
            'João Carlos de Matos',
            'Ângela da Costa e Figueiredo'
        ];

        $results = [];

        foreach ($names as $name) {
            array_push($results, StringHelper::capitalize($name));
        }

        $this->assertEquals($results, $expectedResults);
    }

    public function test_must_capitalize_names_correctly_regardless_of_wheter_they_are_capitalized_or_not(): void
    {
        $names = [
            'ÍCARO DA SILVA',
            'Cássia dos Santos e Silva',
            'verÔnica Das graças e pauLA',
            'joão CARLOS de maTos',
            'ângela da costa E FIGUEIREDO'
        ];

        $expectedResults = [
            'Ícaro da Silva',
            'Cássia dos Santos e Silva',
            'Verônica das Graças e Paula',
            'João Carlos de Matos',
            'Ângela da Costa e Figueiredo'
        ];

        $results = [];

        foreach ($names as $name) {
            array_push($results, StringHelper::capitalize($name));
        }

        $this->assertEquals($results, $expectedResults);
    }

    public function test_must_correctly_capitalize_abbreviated_names_according_to_the_expected_results(): void
    {
        $names = [
            'dr. joão da Silva',
            'dra ângela das graças',
            'sra. cássia dos santos',
            'fernanda l. de nobrega e silva'
        ];

        $expectedResults = [
            'Dr. João da Silva',
            'Dra Ângela das Graças',
            'Sra. Cássia dos Santos',
            'Fernanda L. de Nobrega e Silva'
        ];

        $results = [];

        foreach ($names as $name) {
            array_push($results, StringHelper::capitalize($name));
        }

        $this->assertEquals($results, $expectedResults);
    }

    public function test_must_correctly_remove_unnecessary_whitespace(): void
    {
        $names = [
            '  joão da silva',
            'cristina de paula  ',
            '  fernanda de amorim e santos  ',
            ' guilherme   dos    santos',
            '   '
        ];

        $expectedResults = [
            'João da Silva',
            'Cristina de Paula',
            'Fernanda de Amorim e Santos',
            'Guilherme dos Santos',
            ''
        ];

        $results = [];

        foreach ($names as $name) {
            array_push($results, StringHelper::capitalize($name));
        }

        $this->assertEquals($results, $expectedResults);
    }
}
