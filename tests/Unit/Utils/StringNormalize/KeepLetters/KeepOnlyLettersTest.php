<?php

namespace Tests\Unit\Utils\StringNormalize\KeepLetters;

use App\Utils\StringNormalize;
use Tests\TestCase;

class KeepOnlyLettersTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->keepOnlyLetters()->get());
    }

    public function test_should_return_an_empty_string_when_there_are_no_letters_in_the_string_provided(): void
    {
        $value = '123456.!/@';

        $this->assertEmpty(StringNormalize::for($value)->keepOnlyLetters()->get());
    }

    public function test_should_return_only_the_letters_of_the_string_provided(): void
    {
        $value = '1abcdefghi111';
        $expectedValueNormalized = 'abcdefghi';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->keepOnlyLetters()->get()
        );
    }

    public function test_should_return_only_the_letters_of_the_string_provided_and_ignoring_special_characters(): void
    {
        $value = '1@abc!00.-/|][*$&';
        $expectedValueNormalized = 'abc';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->keepOnlyLetters()->get()
        );
    }
}
