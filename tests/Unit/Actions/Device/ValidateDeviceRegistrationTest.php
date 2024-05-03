<?php

namespace Tests\Unit\Actions\Device;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\User;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ValidateDeviceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private DeviceModel $deviceModel;
    private Device $deviceNotValidated;

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
        $this->deviceModel = DeviceModelFactory::new()
            ->for(BrandFactory::new()->create())
            ->create();

        $this->deviceNotValidated = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create();
    }

    private function invoiceSetUp(): void
    {
        InvoiceFactory::new()
            ->for($this->deviceNotValidated)
            ->create();
    }

    /*
    ================= **START OF TESTS** ==========================================================================
    */

    public function test_should_return_true_when_the_action_is_successful(): void
    {
        $validated = $this->deviceNotValidated->validateRegistration(
            cpf: $this->user->cpf,
            name: $this->user->name,
            products: fake()->text(255)
        );

        $this->assertTrue($validated);
    }

    public function test_the_action_should_change_the_validation_status_from_pending_to_in_analysis(): void
    {
        $this->assertEquals(
            $this->deviceNotValidated->validation_status,
            DeviceValidationStatus::PENDING
        );

        $this->deviceNotValidated->validateRegistration(
            cpf: $this->user->cpf,
            name: $this->user->name,
            products: fake()->text(255)
        );

        $this->assertEquals(
            $this->deviceNotValidated->validation_status,
            DeviceValidationStatus::IN_ANALYSIS
        );
    }

    public function test_the_action_should_update_the_invoice_in_the_fields_consumer_name_consumer_cpf_and_product_description(): void
    {
        $invoice = $this->deviceNotValidated->invoice;
        $productDescription = fake()->text(255);

        $this->deviceNotValidated->validateRegistration(
            cpf: $this->user->cpf,
            name: $this->user->name,
            products: $productDescription
        );

        $this->assertDatabaseHas('invoices', [
            'access_key' => $invoice->access_key,
            'consumer_cpf' => $this->user->cpf,
            'consumer_name' => $this->user->name,
            'product_description' => $productDescription,
            'device_id' => $this->deviceNotValidated->id,
        ]);
    }

    public function test_should_throw_an_exception_when_the_device_validation_status_is_equal_to_in_analysis(): void
    {
        $exceptionOcurred = false;

        $deviceInAnalysis = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::IN_ANALYSIS,
            ]);

        try {
            $deviceInAnalysis->validateRegistration(
                cpf: $this->user->cpf,
                name: $this->user->name,
                products: fake()->text(255),
            );
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_status.must_be_pending'),
            );
        }

        $this->assertTrue($exceptionOcurred);
    }

    public function test_should_throw_an_exception_when_the_device_validation_status_is_equal_to_validated(): void
    {
        $exceptionOcurred = false;

        $deviceValidated = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::VALIDATED,
            ]);

        try {
            $deviceValidated->validateRegistration(
                cpf: $this->user->cpf,
                name: $this->user->name,
                products: fake()->text(255),
            );
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_status.must_be_pending'),
            );
        }

        $this->assertTrue($exceptionOcurred);
    }

    public function test_should_throw_an_exception_when_the_device_validation_status_is_equal_to_rejected(): void
    {
        $exceptionOcurred = false;

        $deviceRejected = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create([
                'validation_status' => DeviceValidationStatus::REJECTED,
            ]);

        try {
            $deviceRejected->validateRegistration(
                cpf: $this->user->cpf,
                name: $this->user->name,
                products: fake()->text(255),
            );
        } catch (Exception $e) {
            $exceptionOcurred = true;

            $this->assertEquals(
                $e->getCode(),
                Response::HTTP_UNPROCESSABLE_ENTITY
            );

            $this->assertEquals(
                $e->getMessage(),
                trans('validation.custom.device_status.must_be_pending'),
            );
        }

        $this->assertTrue($exceptionOcurred);
    }
}
