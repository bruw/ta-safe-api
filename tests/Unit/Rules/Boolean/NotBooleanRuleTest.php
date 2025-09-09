<?php

namespace Tests\Unit\Rules\Boolean;

use App\Rules\NotBoolean;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotBooleanRuleTest extends TestCase
{
    public function test_should_return_true_if_the_attributes_are_not_boolean_values(): void
    {
        $nonBooleanValues = [0, 1, 'true', 'false', Str::random(10)];

        foreach ($nonBooleanValues as $value) {
            $this->assertTrue(
                Validator::make(
                    ['attribute' => $value],
                    ['attribute' => new NotBoolean],
                )->passes(),
                "Error! This attribute value: ($value), is BOOLEAN!"
            );
        }
    }

    public function test_should_return_false_if_the_attributes_are_boolean_values(): void
    {
        $booleanValues = [true, false];

        foreach ($booleanValues as $value) {
            $this->assertFalse(
                Validator::make(
                    ['attribute' => $value],
                    ['attribute' => new NotBoolean]
                )->passes(),
                "Error! This attribute value: ($value), is NOT BOOLEAN!"
            );
        }
    }
}
