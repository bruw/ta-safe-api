<?php

namespace Tests\Unit\Actions\DeviceOwnerInvoiceValidation;

use App\Actions\DeviceOwnerInvoiceValidation\DeviceOwnerCpfValidationAction;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use App\Traits\StringNormalizer;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceOwnerCpfValidationTest extends TestCase
{
    use RefreshDatabase;
    use StringNormalizer;

    private User $user;
    private Device $device;
    private DeviceModel $deviceModel;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSetUp();
        $this->deviceSetUp();
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

        $this->device = DeviceFactory::new()
            ->for($this->user)
            ->for($this->deviceModel)
            ->create();

        $this->invoice = InvoiceFactory::new()
            ->for($this->device)
            ->create([
                'consumer_cpf' => $this->user->cpf,
            ]);
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_the_action_must_be_able_to_validate_the_cpf_of_device_record_when_the_user_cpf_is_identical_to_the_invoice_cpf(): void
    {
        $cpfValidator = new DeviceOwnerCpfValidationAction($this->device);
        $result = $cpfValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_new_record_in_the_database_with_the_validation_log_when_the_action_is_successful(): void
    {
        $cpfValidator = new DeviceOwnerCpfValidationAction($this->device);
        $cpfValidator->execute();

        $cpf = $this->extractOnlyDigits($this->user->cpf);

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => User::class,
            'attribute_label' => 'cpf',
            'attribute_value' => $cpf,
            'invoice_attribute_label' => 'consumer_cpf',
            'invoice_attribute_value' => $cpf,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 100,
            'validated' => true,
        ]);
    }

    public function test_the_action_must_not_be_able_to_validate_a_cpf_with_missing_digits(): void
    {
        $this->invoice->update([
            'consumer_cpf' => substr($this->user->cpf, 0, -1),
        ]);

        $cpfValidator = new DeviceOwnerCpfValidationAction($this->device);
        $result = $cpfValidator->execute();

        $this->assertFalse($result->validated);
    }

    public function test_the_action_must_not_be_able_to_validate_a_cpf_with_extra_digits(): void
    {
        $cpfWithExtraDigit = $this->user->cpf . '0';

        $this->invoice->update([
            'consumer_cpf' => $cpfWithExtraDigit,
        ]);

        $cpfValidator = new DeviceOwnerCpfValidationAction($this->device);
        $result = $cpfValidator->execute();

        $this->assertFalse($result->validated);
    }

    public function test_the_action_must_not_be_able_to_validate_a_cpf_that_is_opposite_of_the_reference_value(): void
    {
        $this->invoice->update([
            'consumer_cpf' => strrev($this->user->cpf),
        ]);

        $cpfValidator = new DeviceOwnerCpfValidationAction($this->device);
        $result = $cpfValidator->execute();

        $this->assertFalse($result->validated);
    }

}
