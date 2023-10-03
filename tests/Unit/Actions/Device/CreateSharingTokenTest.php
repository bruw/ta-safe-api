<?php

namespace Tests\Unit\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\DeviceSharingToken;
use App\Models\Invoice;
use App\Models\User;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CreateSharingTokenTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->owner = User::factory()->create();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device = Device::factory()
            ->for($this->owner)
            ->for($deviceModel)
            ->has(Invoice::factory())
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED
            ]);
    }

    public function test_should_return_true_if_the_share_token_is_successfully_created(): void
    {
        $this->assertTrue(
            $this->device->createSharingToken()
        );
    }

    public function test_should_return_true_if_the_token_is_valid_for_24_hours(): void
    {
        $this->assertTrue(
            $this->device->createSharingToken()
        );

        $this->device->refresh();

        $tokenValidity = now()->diffInRealHours(
            $this->device->sharingToken->expires_at
        );

        $this->assertTrue($tokenValidity == 24);
    }

    public function test_when_a_token_already_exists_it_must_be_updated_without_generating_a_new_record_in_the_database(): void
    {
        $this->assertTrue($this->device->sharingToken()->count() == 0);

        $this->device->createSharingToken();
        $this->device->refresh();

        $this->assertTrue($this->device->sharingToken()->count() == 1);
        $oldTokenData = $this->device->sharingToken;

        $this->device->createSharingToken();
        $this->device->refresh();

        $this->assertTrue($this->device->sharingToken()->count() == 1);
        $newTokenData = $this->device->sharingToken;

        $this->assertEquals(
            $newTokenData->id,
            $oldTokenData->id
        );

        $this->assertNotEquals(
            $newTokenData->token,
            $oldTokenData->token
        );
    }

    public function test_should_throw_an_exception_if_the_device_record_has_not_yet_been_validated(): void
    {
        $this->device->update([
            'validation_status' => DeviceValidationStatus::PENDING
        ]);

        $exceptionOcurred = false;

        try {
            $this->device->createSharingToken();
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_sharing_token.register_not_validated')
            );
        }

        $this->assertTrue($exceptionOcurred);
    }

    public function test_should_return_an_exception_if_an_internal_error_occurs_when_trying_to_create_a_token(): void
    {
        $lastDevice = Device::latest('id')->first();

        $this->device->id = $lastDevice->id + 1;

        try {
            $this->device->createSharingToken();
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_sharing_token.unable_to_create_token'),
            );
        }

        $this->assertTrue($exceptionOcurred);
    }
}
