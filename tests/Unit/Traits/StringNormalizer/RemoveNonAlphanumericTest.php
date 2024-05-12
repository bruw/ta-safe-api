<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use Tests\TestCase;

class RemoveNonAlphanumericTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(
            $this->removeNonAlphanumeric(null),
        );
    }

    public function test_should_not_modify_a_string_that_already_has_only_alphanumeric_content(): void
    {
        $inputString = 'It has nothing to do with whether its possible or not I do it because I want to';

        $this->assertEquals(
            $this->removeNonAlphanumeric($inputString),
            $inputString
        );
    }

    public function test_should_return_an_alphanumeric_string(): void
    {
        $inputString = "It: h@s nothing~ (to) d* with whether it's possible or not. I do it because I want to!+";
        $expectedResult = 'It hs nothing to d with whether its possible or not I do it because I want to';

        $this->assertEquals(
            $this->removeNonAlphanumeric($inputString),
            $expectedResult
        );
    }
}
