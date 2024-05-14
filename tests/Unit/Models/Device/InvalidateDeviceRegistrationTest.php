<?php

namespace Tests\Unit\Models\User\Collections;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvalidateDeviceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceSetUp();
        $this->invoiceSetUp();
    }

    /*
    ================= **START OF SETUP** ==========================================================================
    */

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $deviceModel = DeviceModelFactory::new()
            ->for(BrandFactory::new()->create())
            ->create();

        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($deviceModel)
            ->create();
    }

    private function invoiceSetUp(): void
    {
        InvoiceFactory::new()
            ->for($this->device)
            ->create();
    }

    /*
    ================= **START OF TESTS** ==========================================================================
    */

    public function test_should_return_true_when_the_update_is_successful(): void
    {
        $this->assertTrue(
            $this->device->invalidateRegistration()
        );
    }

    public function test_must_invalidate_a_device_registration_that_has_a_pending_status(): void
    {
        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::PENDING
        );

        $this->device->invalidateRegistration();

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );
    }

    public function test_must_not_change_a_device_registration_with_in_analysis_status(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::IN_ANALYSIS,
        ]);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::IN_ANALYSIS
        );

        $this->assertFalse(
            $this->device->invalidateRegistration()
        );

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::IN_ANALYSIS
        );
    }

    public function test_must_not_change_a_device_registration_with_validated_status(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::VALIDATED,
        ]);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::VALIDATED
        );

        $this->assertFalse(
            $this->device->invalidateRegistration()
        );

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::VALIDATED
        );
    }

    public function test_must_not_change_a_device_registration_with_rejected_status(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::REJECTED,
        ]);

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );

        $this->assertFalse(
            $this->device->invalidateRegistration()
        );

        $this->assertEquals(
            $this->device->validation_status,
            DeviceValidationStatus::REJECTED
        );
    }
}
