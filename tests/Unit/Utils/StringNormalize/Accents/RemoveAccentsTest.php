<?php

namespace Tests\Unit\Utils\StringNormalize\Accents;

use App\Utils\StringNormalize;
use Tests\TestCase;

class RemoveAccentsTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->removeAccents()->get());
    }

    public function test_should_return_the_original_string_when_there_are_no_accents_in_it(): void
    {
        $value = 'Cable ahoy parley dance the hempen jig belay gibbet';
        $this->assertEquals($value, StringNormalize::for($value)->removeAccents()->get());
    }

    public function test_should_remove_all_accents_from_the_string_when_it_has_them(): void
    {
        $value = 'áéíóúãõâêîôûàèìòùçÁÉÍÓÚÃÕÂÊÎÔÛÀÈÌÒÙÇ';
        $expectedValueNormalized = 'aeiouaoaeiouaeioucAEIOUAOAEIOUAEIOUC';

        $this->assertEquals(
            $expectedValueNormalized,
            StringNormalize::for($value)->removeAccents()->get()
        );
    }
}
