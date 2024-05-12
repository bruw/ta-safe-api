<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use Tests\TestCase;

class BasicNormalizeTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(
            $this->extractOnlyDigits(null),
        );
    }

    public function test_should_not_modify_a_string_that_is_already_normalized(): void
    {
        $inputString = 'cafe e bao';

        $this->assertEquals(
            $this->removeNonAlphanumeric($inputString),
            $inputString
        );
    }

    public function test_should_return_a_lowercase_string_without_accents_and_extra_whitespace(): void
    {
        $inputString = ' CAFÉ É   BÃO DEMAIS   ';
        $expectedResult = 'cafe e bao demais';

        $this->assertEquals(
            $this->basicNormalize($inputString),
            $expectedResult
        );
    }
}
