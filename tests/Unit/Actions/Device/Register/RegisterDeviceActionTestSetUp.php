<?php

namespace Tests\Unit\Actions\Device\Register;

use App\Dto\Device\RegisterDeviceDto;
use App\Models\User;
use App\Traits\RandomNumberGenerator;
use Database\Factories\DeviceModelFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterDeviceActionTestSetUp extends TestCase
{
    use RandomNumberGenerator;
    use RefreshDatabase;

    protected User $user;
    protected RegisterDeviceDto $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceDataSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceDataSetUp(): void
    {
        $deviceModel = DeviceModelFactory::new()->create();

        $this->data = new RegisterDeviceDto(
            deviceModelId: $deviceModel->id,
            accessKey: $this->generateRandomNumber(44),
            color: 'black',
            imei1: $this->generateRandomNumber(15),
            imei2: $this->generateRandomNumber(15),
        );
    }
}
