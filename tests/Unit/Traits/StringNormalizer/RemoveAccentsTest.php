<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use Tests\TestCase;

class RemoveAccentsTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_the_original_string_when_there_are_no_accents_in_it(): void
    {
        $inputString = 'Cable ahoy parley dance the hempen jig belay gibbet';

        $this->assertEquals(
            $this->removeAccents($inputString),
            $inputString
        );
    }

    public function test_should_remove_all_accents_from_the_string_when_it_has_them(): void
    {
        $inputString = 'áéíóúãõâêîôûàèìòùçÁÉÍÓÚÃÕÂÊÎÔÛÀÈÌÒÙÇ';
        $expectedResult = 'aeiouaoaeiouaeioucAEIOUAOAEIOUAEIOUC';

        $this->assertEquals(
            $this->removeAccents($inputString),
            $expectedResult
        );
    }
}
