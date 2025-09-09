<?php

namespace Tests\Unit\Actions\Device\Token;

use App\Exceptions\HttpJsonResponseException;
use App\Models\DeviceSharingToken;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CreateSharingTokenActionTest extends CreateSharingTokenActionTestSetUp
{
    public function test_should_return_a_device_sharing_token_when_the_action_is_successful(): void
    {
        $token = $this->user->deviceService()->createSharingToken($this->device);
        $this->assertInstanceOf(DeviceSharingToken::class, $token);
    }

    public function test_the_token_should_be_valid_for_24_hours(): void
    {
        $token = $this->user->deviceService()->createSharingToken($this->device);

        $tokenValidity = now()->diffInRealHours($token->expires_at);
        $this->assertEqualsWithDelta($tokenValidity, 24, 0.001);
    }

    public function should_thrown_an_exception_when_the_user_is_not_the_device_owner(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.user.owner'));

        $user = UserFactory::new()->create();
        $user->deviceService()->createSharingToken($this->device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_validation_is_pending(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.validated'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->create();

        $this->user->deviceService()->createSharingToken($device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_validation_is_in_analysis(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.validated'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->inAnalysis()
            ->create();

        $this->user->deviceService()->createSharingToken($device);
    }

    public function test_should_thrown_an_exception_when_the_device_status_validation_is_rejected(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.validated'));

        $device = DeviceFactory::new()
            ->for($this->user)
            ->rejected()
            ->create();

        $this->user->deviceService()->createSharingToken($device);
    }

    public function test_should_thrown_an_exception_when_occurred_an_error_internal(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device.errors.token'));

        DB::shouldReceive('transaction')->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            ));

        $this->user->deviceService()->createSharingToken($this->device);
    }

}
