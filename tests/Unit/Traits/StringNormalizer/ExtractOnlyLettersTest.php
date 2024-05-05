<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use Tests\TestCase;

class ExtractOnlyLettersTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(
            $this->extractOnlyLetters(null)
        );
    }

    public function test_should_return_an_empty_string_when_there_are_no_letters_in_the_input_string(): void
    {
        $inputString = '123456.!/@';

        $this->assertEmpty(
            $this->extractOnlyLetters($inputString)
        );
    }

    public function test_should_return_only_the_letters_of_the_input_string(): void
    {
        $inputstring = '1abcdefghi111';
        $expectedResult = 'abcdefghi';

        $this->assertEquals(
            $this->extractOnlyLetters($inputstring),
            $expectedResult
        );
    }

    public function test_should_return_the_letters_of_the_input_string_removing_the_extra_whitespaces(): void
    {
        $inputString = '1 abc0 defghi 11';
        $expectedResult = 'abc defghi';

        $this->assertEquals(
            $this->extractOnlyLetters($inputString),
            $expectedResult
        );
    }

    public function test_should_return_the_letters_of_string_converted_to_lowercase(): void
    {
        $inputString = '1 ABCDEfg BOOMM1';
        $expectedResult = 'abcdefg boomm';

        $this->assertEquals(
            $this->extractOnlyLetters($inputString),
            $expectedResult
        );
    }

    public function test_should_return_only_the_letters_of_the_input_string_ignoring_special_characters(): void
    {
        $inputString = '1@abc!00.-/|][*$&';
        $expectedResult = 'abc';

        $this->assertEquals(
            $this->extractOnlyLetters($inputString),
            $expectedResult
        );
    }

    public function test_should_return_only_the_letters_of_the_input_string_removing_extra_whitespaces_and_igoring_special_characters(): void
    {
        $inputString = '1@ abc! DE00. -/x 1';
        $expectedResult = 'abc de x';

        $this->assertEquals(
            $this->extractOnlyLetters($inputString),
            $expectedResult
        );
    }
}
