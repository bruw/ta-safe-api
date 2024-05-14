<?php

namespace Tests\Unit\Traits\StringNormalizer;

use App\Traits\StringNormalizer;
use Tests\TestCase;

class NormalizeMemorySizeTest extends TestCase
{
    use StringNormalizer;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_should_return_an_empty_string_when_the_param_is_a_null_value(): void
    {
        $this->assertEmpty(
            $this->normalizeMemorySize(null),
        );
    }

    public function test_should_not_modify_a_string_that_is_already_normalized(): void
    {
        $inputString = '16gb';

        $this->assertEquals(
            $this->normalizeMemorySize($inputString),
            $inputString
        );
    }

    public function test_should_not_normalize_when_it_does_not_have_a_number_before_the_format(): void
    {
        $values = [
            'GB',
            'gb',
            '4 g',
            'GB 16',
            '12a GB',
            '4gb.',
            'Samsung galaxy GB12',
        ];

        foreach ($values as $value) {
            $this->assertEquals(
                $this->normalizeMemorySize($value),
                $value
            );
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

        foreach ($values as $originalValue => $expectedNormalizedValue) {
            $normalizedValue = $this->normalizeMemorySize($originalValue);
            $this->assertEquals($expectedNormalizedValue, $normalizedValue);
        }
    }
}
