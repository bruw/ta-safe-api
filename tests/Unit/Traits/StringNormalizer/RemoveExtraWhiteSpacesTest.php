<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use PHPUnit\Framework\TestCase;

class RemoveExtraWhiteSpacesTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_the_input_string_unmodified_when_there_no_extra_whitespaces(): void
    {
        $inputString = 'ab c';

        $this->assertEquals(
            $this->removeExtraWhiteSpaces($inputString),
            $inputString
        );
    }

    public function test_should_return_a_formatted_string_with_no_extra_whitespaces_in_the_middle_of_the_input_string(): void
    {
        $inputstring = '123   abc';
        $expectedResult = '123 abc';

        $this->assertEquals(
            $this->removeExtraWhiteSpaces($inputstring),
            $expectedResult
        );
    }

    public function test_should_return_a_formatted_string_without_white_spaces_at_the_beginning_and_end_of_the_input_string(): void
    {
        $inputString = ' 123abc ';
        $expectedResult = '123abc';

        $this->assertEquals(
            $this->removeExtraWhiteSpaces($inputString),
            $expectedResult
        );
    }
}
