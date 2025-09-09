<?php

namespace Tests\Unit\Utils\StringNormalize\Default;

use App\Utils\StringNormalize;
use Tests\TestCase;

class DefaultNormalizeTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->defaultNormalize()->get());
    }

    public function test_should_not_modify_a_string_that_is_already_normalized(): void
    {
        $value = 'cafe e bao';
        $this->assertEquals($value, StringNormalize::for($value)->defaultNormalize()->get());
    }

    public function test_should_return_a_lowercase_string_without_accents_and_without_extra_white_spaces(): void
    {
        $value = ' CAFÉ É   BÃO DEMAIS   ';
        $expectedValueNormalized = 'cafe e bao demais';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->defaultNormalize()->get(),
        );
    }
}
