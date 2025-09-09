<?php

namespace Tests\Unit\Models\DeviceSharingToken;

use Database\Factories\DeviceSharingTokenFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceSharingTokenExpiredTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_true_if_token_is_expired(): void
    {
        $sharingToken = DeviceSharingTokenFactory::new()->expired()->create();
        $this->assertTrue($sharingToken->isExpired());
    }

    public function test_should_false_if_token_is_not_expired(): void
    {
        $sharingToken = DeviceSharingTokenFactory::new()->create();
        $this->assertFalse($sharingToken->isExpired());
    }
}
