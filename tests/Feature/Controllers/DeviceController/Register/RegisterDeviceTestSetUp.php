<?php

namespace Tests\Feature\Controllers\DeviceController\Register;

use App\Models\Device;
use App\Models\User;
use App\Traits\RandomNumberGenerator;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class RegisterDeviceTestSetUp extends TestCase
{
    use AccessAsserts;
    use RandomNumberGenerator;
    use RefreshDatabase;

    protected User $user;
    protected Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->userSetUp();
        $this->deviceSetUp();
    }

    /**
     * Set up a new user instance using the UserFactory.
     */
    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    /**
     * Create a validated device for the given user.
     */
    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->validated()
            ->create();
    }

    /**
     * Get the route for the register device endpoint.
     */
    public function route(): string
    {
        return route('api.device.register');
    }

    /**
     * Generate a valid data set for the register device endpoint.
     */
    public function data(array $overrides = []): array
    {
        return array_merge([
            'device_model_id' => $this->device->deviceModel->id,
            'access_key' => $this->generateRandomNumber(44),
            'color' => 'black',
            'imei_1' => $this->generateRandomNumber(15),
            'imei_2' => $this->generateRandomNumber(15),
        ], $overrides);
    }

}
