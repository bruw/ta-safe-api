<?php

namespace Tests\Feature\Controllers\DeviceController\Validation;

use App\Models\Device;
use App\Models\User;
use App\Traits\RandomNumberGenerator;
use Database\Factories\DeviceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Asserts\AccessAsserts;
use Tests\TestCase;

class StartValidateDeviceTestSetUp extends TestCase
{
    use AccessAsserts;
    use RefreshDatabase;
    use RandomNumberGenerator;

    protected User $user;
    protected Device $device;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceSetUp();
    }

    private function userSetUp(): void
    {
        $this->user = UserFactory::new()->create();
    }

    private function deviceSetUp(): void
    {
        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->create();
    }

    protected function route(array $overrides = []): string
    {
        return route('api.device.validation', ['device' => $this->device]);
    }

    protected function data(array $overrides = []): array
    {
        $products = "{$this->device->deviceModel->brand->name} "
            . "{$this->device->deviceModel->name} "
            . "{$this->device->color} "
            . "{$this->device->imei_1}"
            . "{$this->device->imei_2}"
            . "{$this->device->ram}"
            . "{$this->device->storage}";

        return array_merge([
            'name' => $this->user->name,
            'cpf' => $this->user->cpf,
            'products' => $products,
        ], $overrides);
    }
}
