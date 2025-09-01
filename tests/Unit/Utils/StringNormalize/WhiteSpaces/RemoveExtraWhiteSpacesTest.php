<?php

namespace Tests\Unit\Utils\StringNormalize\WhiteSpaces;

use App\Utils\StringNormalize;
use Tests\TestCase;

class RemoveExtraWhiteSpacesTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->removeExtraWhiteSpaces()->get());
    }

    public function test_should_return_the_string_value_unmodified_when_there_no_extra_whitespaces(): void
    {
        $value = 'ab c';
        $this->assertEquals($value, StringNormalize::for($value)->removeExtraWhiteSpaces()->get());
    }

    public function test_should_return_a_formatted_string_with_no_extra_whitespaces_in_the_middle_of_the_input_string(): void
    {
        $value = '123   abc';
        $expectedValueNormalized = '123 abc';

        $this->assertEquals(
            $expectedValueNormalized, 
            StringNormalize::for($value)->removeExtraWhiteSpaces()->get()
        );
    }

    public function test_should_return_a_formatted_string_without_whitespaces_at_the_beginning_and_end_of_the_string_value(): void
    {
        $value= ' 123abc ';
        $expectedValueNormalized = '123abc';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->removeExtraWhiteSpaces()->get()
        );
    }
}
