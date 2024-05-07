<?php

namespace Tests\Unit\Actions\ValidateDeviceRegistration;

use App\Actions\DeviceOwnerInvoiceValidation\DeviceOwnerNameValidationAction;
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

class NameAttributeValidationTest extends TestCase
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
                'consumer_name' => $this->user->name,
            ]);
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_must_validate_identical_names(): void
    {
        $nameSimilarityValidator = new DeviceOwnerNameValidationAction($this->device);
        $result = $nameSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 100);
    }

    public function test_should_generate_a_record_in_the_database_for_successful_validations(): void
    {
        $nameSimilarityValidator = new DeviceOwnerNameValidationAction($this->device);
        $nameSimilarityValidator->execute();

        $name = $this->extractOnlyLetters($this->user->name);

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->user->id,
            'device_id' => $this->device->id,
            'attribute_context' => get_class($this->user),
            'attribute_name' => 'name',
            'attribute_value' => $name,
            'provided_value' => $name,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 75,
            'validated' => true,
        ]);
    }

    public function test_the_action_must_validate_with_a_similarity_ratio_of_more_than_75_porcent(): void
    {
        $this->user->update([
            'name' => 'João Paulo da Silva',
        ]);

        $this->invoice->update([
            'consumer_name' => 'João P. Silva',
        ]);

        $nameSimilarityValidator = new DeviceOwnerNameValidationAction($this->device);
        $result = $nameSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 77);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_the_should_not_validate_names_with_a_similarity_ratio_of_less_than_75_percent(): void
    {
        $this->user->update([
            'name' => 'João Paulo da Silva',
        ]);

        $this->invoice->update([
            'consumer_name' => 'João J. Silva',
        ]);

        $nameSimilarityValidator = new DeviceOwnerNameValidationAction($this->device);
        $result = $nameSimilarityValidator->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 70);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_should_validate_similar_names_even_if_they_have_accents_or_other_special_characters(): void
    {
        $this->user->update([
            'name' => 'Luísa Leônidas Gonçalves-Santos',
        ]);

        $this->invoice->update([
            'consumer_name' => 'Luisa L. GoncalVÉS santos.',
        ]);

        $nameSimilarityValidator = new DeviceOwnerNameValidationAction($this->device);
        $result = $nameSimilarityValidator->execute();

        $this->assertTrue($result->validated);
        $this->assertEquals($result->similarity_ratio, 85);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }

    public function test_should_not_validate_names_that_are_empty_strings(): void
    {
        $this->user->update([
            'name' => '',
        ]);

        $this->invoice->update([
            'consumer_name' => '',
        ]);

        $nameSimilarityValidator = new DeviceOwnerNameValidationAction($this->device);
        $result = $nameSimilarityValidator->execute();

        $this->assertFalse($result->validated);
        $this->assertEquals($result->similarity_ratio, 0);
        $this->assertEquals($result->min_similarity_ratio, 75);
    }
}
