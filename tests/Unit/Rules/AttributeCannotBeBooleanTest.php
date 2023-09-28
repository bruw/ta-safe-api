<?php

namespace Tests\Unit\Rules;

use App\Rules\AttributeCannotBeBoolean;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class AttributeCannotBeBooleanTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_true_if_the_attributes_are_not_boolean_values(): void
    {
        $nonBooleanValues = [
            0, 1, 'true', 'false', Str::random(10), fake()->name(),
            fake()->phoneNumber(), fake()->text()
        ];

        foreach ($nonBooleanValues as $value) {
            $this->assertTrue(
                Validator::make(
                    ['attribute' => $value],
                    ['attribute' => new AttributeCannotBeBoolean],
                )->passes(),
                "Error! This attribute value: ($value), is NOT VALID!"
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
                    ['attribute' => new AttributeCannotBeBoolean]
                )->passes(),
                "Error! This attribute value: ($value), is VALID!"
            );
        }
    }
}
