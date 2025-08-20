<?php

namespace Tests\Unit\Rules\DeviceSharingToken;

use App\Rules\ExpiredDeviceSharingTokenRule;
use App\Traits\RandomNumberGenerator;
use Database\Factories\DeviceSharingTokenFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ExpiredDeviceSharingTokenRuleTest extends TestCase
{
    use RandomNumberGenerator;
    use RefreshDatabase;

    public function test_should_return_true_when_the_token_is_valid(): void
    {
        $sharingToken = DeviceSharingTokenFactory::new()->create();

        $validator = Validator::make(
            ['attribute' => $sharingToken->token],
            ['attribute' => new ExpiredDeviceSharingTokenRule]
        );

        $this->assertTrue($validator->passes());
    }

    public function test_should_return_false_and_an_error_message_when_the_token_does_not_exists(): void
    {
        $sharingToken = $this->generateRandomNumber(8);

        $validator = Validator::make(
            ['attribute' => $sharingToken],
            ['attribute' => new ExpiredDeviceSharingTokenRule]
        );

        $this->assertFalse($validator->passes());

        $this->assertEquals(
            trans('validation.custom.token.exists'),
            $validator->errors()->first('attribute')
        );

    }

    public function test_should_return_false_and_an_error_message_when_the_token_is_expired(): void
    {
        $sharingToken = DeviceSharingTokenFactory::new()->expired()->create();

        $validator = Validator::make(
            ['attribute' => $sharingToken->token],
            ['attribute' => new ExpiredDeviceSharingTokenRule]
        );

        $this->assertFalse($validator->passes());

        $this->assertEquals(
            trans('validation.custom.token.expired'),
            $validator->errors()->first('attribute')
        );
    }
}
