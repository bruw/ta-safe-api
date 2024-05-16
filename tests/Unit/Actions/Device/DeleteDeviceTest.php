<?php

namespace Tests\Unit\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class DeleteDeviceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private DeviceModel $deviceModel;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->deviceModel = DeviceModelFactory::new()
            ->for(BrandFactory::new()->create())
            ->create();

        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::REJECTED,
            ]);
    }

    public function test_should_return_true_when_the_action_is_successful(): void
    {
        $this->assertTrue($this->device->safeDelete());
    }

    public function test_should_be_possible_delete_a_device_when_the_status_is_rejected(): void
    {
        $this->assertTrue(
            $this->device->safeDelete()
        );

        $this->assertTrue(
            Device::withTrashed()->find($this->device->id)->trashed()
        );
    }

    public function test_should_throw_an_exception_when_the_validation_status_is_pending(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::PENDING,
        ]);

        $exceptionOcurred = false;

        try {
            $this->device->safeDelete();
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_delete.invalid'),
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertFalse(
            Device::withTrashed()->find($this->device->id)->trashed()
        );
    }

    public function test_should_throw_an_exception_when_the_validation_status_is_in_analysis(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::IN_ANALYSIS,
        ]);

        $exceptionOcurred = false;

        try {
            $this->device->safeDelete();
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_delete.invalid'),
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertFalse(
            Device::withTrashed()->find($this->device->id)->trashed()
        );
    }

    public function test_should_throw_an_exception_when_the_validation_status_is_in_validated(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::VALIDATED,
        ]);

        $exceptionOcurred = false;

        try {
            $this->device->safeDelete();
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_delete.invalid'),
            );
        }

        $this->assertTrue($exceptionOcurred);

        $this->assertFalse(
            Device::withTrashed()->find($this->device->id)->trashed()
        );
    }
}
