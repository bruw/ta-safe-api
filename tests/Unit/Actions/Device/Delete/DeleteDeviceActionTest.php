<?php

namespace Tests\Unit\Actions\Device\Delete;

use App\Exceptions\HttpJsonResponseException;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DeleteDeviceActionTest extends DeleteDeviceActionSetUpTest
{
    public function test_should_throw_an_exception_when_the_user_is_not_owner_of_the_device(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.user.owner'));

        $this->deviceRejected->safeDelete(UserFactory::new()->create());
        $this->assertDatabaseHas('devices', ['id' => $this->deviceRejected->id]);
    }

    public function test_should_be_possible_to_delete_a_device_that_has_a_rejected_validation_status(): void
    {
        $this->assertTrue($this->deviceRejected->safeDelete($this->user));
        $this->assertDatabaseMissing('devices', ['id' => $this->deviceRejected->id]);
    }

    public function test_should_throw_an_exception_when_the_device_validation_status_is_pending(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.rejected'));

        $this->devicePending->safeDelete($this->user);
        $this->assertDatabaseHas('devices', ['id' => $this->devicePending->id]);
    }

    public function test_should_throw_an_exception_when_the_validation_status_is_in_analysis(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.rejected'));

        $this->deviceInAnalysis->safeDelete($this->user);
        $this->assertDatabaseHas('devices', ['id' => $this->deviceInAnalysis->id]);
    }

    public function test_should_throw_an_exception_when_the_validation_status_is_in_validated(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->expectExceptionMessage(trans('validators.device.status.rejected'));

        $this->deviceValidated->safeDelete($this->user);
        $this->assertDatabaseHas('devices', ['id' => $this->deviceValidated->id]);
    }

    public function test_should_thrown_an_exception_when_an_internal_error_occurs(): void
    {
        $this->expectException(HttpJsonResponseException::class);
        $this->expectExceptionCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $this->expectExceptionMessage(trans('actions.device.errors.delete'));

        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new Exception('Simulates a transaction error',
                Response::HTTP_INTERNAL_SERVER_ERROR)
            );

        $this->deviceRejected->safeDelete($this->user);
    }
}
