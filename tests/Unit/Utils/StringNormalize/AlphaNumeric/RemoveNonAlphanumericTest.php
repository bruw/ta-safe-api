<?php

namespace Tests\Unit\Utils\StringNormalize\AlphaNumeric;

use App\Utils\StringNormalize;
use Tests\TestCase;

class RemoveNonAlphanumericTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->removeNonAlphanumeric()->get());
    }

    public function test_should_not_modify_a_string_that_already_has_only_alphanumeric_content(): void
    {
        $value = 'It has nothing to do with whether its possible or not I do it because I want to';
        $this->assertEquals($value, StringNormalize::for($value)->removeNonAlphanumeric()->get());
    }

    public function test_should_return_an_alphanumeric_string(): void
    {
        $value = "It: h@s nothing~ (to) d* with whether it's possible or not. I do it because I want to!+";
        $expectedValueNormalized = 'It hs nothing to d with whether its possible or not I do it because I want to';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->removeNonAlphanumeric()->get()
        );
    }
}
