<?php

namespace Tests\Unit\Actions\Device\Create;

use App\Dto\Device\CreateDeviceDto;
use App\Models\Device;
use App\Models\User;
use App\Traits\RandomNumberGenerator;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateDeviceActionSetUpTest extends TestCase
{
    use RandomNumberGenerator;
    use RefreshDatabase;

    protected User $user;
    protected Device $device;
    protected CreateDeviceDto $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->device = DeviceFactory::new()->for($this->user)->create();

        $this->data = new CreateDeviceDto(
            deviceModelId: $this->device->deviceModel->id,
            accessKey: $this->generateRandomNumber(44),
            color: 'black',
            imei1: $this->generateRandomNumber(15),
            imei2: $this->generateRandomNumber(15)
        );
    }
}
