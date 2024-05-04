<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use Tests\TestCase;

class ExtractOnlyDigitsTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_an_empty_string_when_there_are_no_digits_in_the_input_string(): void
    {
        $inputString = 'abcDEFghi';

        $this->assertEmpty(
            $this->extractOnlyDigits($inputString)
        );
    }

    public function test_should_return_only_the_digits_of_the_input_string(): void
    {
        $inputstring = '1abc0DEFghi111';
        $expectedResult = '10111';

        $this->assertEquals(
            $this->extractOnlyDigits($inputstring),
            $expectedResult
        );
    }

    public function test_should_return_the_digits_of_the_input_string_respecting_the_spacing(): void
    {
        $inputString = '1 abc0 DEFghi 11';
        $expectedResult = '1 0  11';

        $this->assertEquals(
            $this->extractOnlyDigits($inputString),
            $expectedResult
        );
    }

    public function test_should_return_only_the_digits_of_the_input_string_ignoring_special_characters(): void
    {
        $inputString = '1@abc!DE00.-/';
        $expectedResult = '100';

        $this->assertEquals(
            $this->extractOnlyDigits($inputString),
            $expectedResult
        );
    }

    public function test_should_return_only_the_digits_of_the_input_string_respecting_spacing_and_ignoring_special_characters(): void
    {
        $inputString = '1@ abc! DE00. -/x 1';
        $expectedResult = '1  00  1';

        $this->assertEquals(
            $this->extractOnlyDigits($inputString),
            $expectedResult
        );
    }
}
