<?php

namespace Tests\Unit\Utils\StringNormalize\KeepDigits;

use App\Utils\StringNormalize;
use Tests\TestCase;

class KeepOnlyDigitsTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->keepOnlyDigits()->get());
    }

    public function test_should_return_an_empty_string_when_there_are_no_digits_in_the_string_provided(): void
    {
        $value = 'abcDEFghi';

        $this->assertEmpty(StringNormalize::for($value)->keepOnlyDigits()->get());
    }

    public function test_should_return_only_the_digits_of_the_string_provided(): void
    {
        $value = '1abc0DEFghi111';
        $expectedValueNormalized = '10111';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->keepOnlyDigits()->get()
        );
    }

    public function test_should_return_only_the_digits_of_string_provided_and_ignoring_special_characters(): void
    {
        $value = '1@abc!DE00.-/';
        $expectedValueNormalized = '100';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->keepOnlyDigits()->get()
        );
    }
}
