<?php

namespace Tests\Unit\Utils\StringNormalize\Memory;

use App\Utils\StringNormalize;
use Tests\TestCase;

class NormalizeMemorySizeTest extends TestCase
{
    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(StringNormalize::for(null)->normalizeMemorySize()->get());
    }

    public function test_should_not_modify_a_string_that_is_already_normalized(): void
    {
        $value = '16gb';
        $this->assertEquals($value, StringNormalize::for($value)->normalizeMemorySize()->get());
    }

    public function test_should_not_normalize_a_string_when_the_value_provided_does_not_have_the_expected_memory_format(): void
    {
        $values = ['GB', 'gb', '4 g', 'GB 16', '12a GB', '4gb.', 'Samsung galaxy GB12'];

        foreach ($values as $value) {
            $this->assertEquals($value, StringNormalize::for($value)->normalizeMemorySize()->get());
        }
    }

    public function test_should_normalize_the_string_when_it_has_a_number_and_the_unit_of_measure_to_gigabyte_after_it(): void
    {
        $values = [
            '1 GB' => '1gb',
            '256Gb' => '256gb',
            '8 gb' => '8gb',
            '4 gB' => '4gb',
        ];

        foreach ($values as $value => $expectedNormalizedValue) {
            $this->assertEquals(
                $expectedNormalizedValue,
                StringNormalize::for($value)->normalizeMemorySize()->get()
            );
        }
    }
}
