<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\User\Name;

use App\Actions\DeviceInvoice\Validation\User\Name\DeviceOwnerNameValidationAction;
use App\Constants\DeviceAttributeValidationRatio;
use App\Models\DeviceAttributeValidationLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DeviceOwnerNameValidationActionTest extends DeviceOwnerNameValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceOwnerNameValidationAction($this->device))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceOwnerNameValidationAction($this->device))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => User::class,
            'attribute_label' => 'user_name',
            'attribute_value' => $this->user->name,
            'invoice_attribute_label' => 'consumer_name',
            'invoice_attribute_value' => $this->device->invoice->consumer_name,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => DeviceAttributeValidationRatio::MIN_NAME_SIMILARITY,
            'validated' => true,
        ]);
    }

    public function test_identical_name_values_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceOwnerNameValidationAction($this->device))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_the_action_must_validate_with_a_similarity_ratio_of_more_than_75_porcent(): void
    {
        $this->user->update(['name' => 'João Paulo da Silva']);
        $this->user->refresh();

        $this->device->invoice->update(['consumer_name' => 'João P. Silva']);
        $this->device->refresh();

        $log = (new DeviceOwnerNameValidationAction($this->device))->execute();

        $this->assertTrue($log->validated);
        $this->assertEquals(77, $log->similarity_ratio);
        $this->assertEquals(75, $log->min_similarity_ratio);
    }

    public function test_the_should_not_validate_names_with_a_similarity_ratio_of_less_than_75_percent(): void
    {
        $this->user->update(['name' => 'João Paulo da Silva']);
        $this->user->refresh();

        $this->device->invoice->update(['consumer_name' => 'João J. Silva']);
        $this->device->refresh();

        $log = (new DeviceOwnerNameValidationAction($this->device))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(70, $log->similarity_ratio);
        $this->assertEquals(75, $log->min_similarity_ratio);
    }

    public function test_should_validate_similar_names_even_if_they_have_accents_or_other_special_characters(): void
    {
        $this->user->update(['name' => 'Luísa Leônidas Gonçalves-Santos']);
        $this->user->refresh();

        $this->device->invoice->update(['consumer_name' => 'Luisa L. GoncalVÉS santos.']);
        $this->device->refresh();

        $log = (new DeviceOwnerNameValidationAction($this->device))->execute();

        $this->assertTrue($log->validated);
        $this->assertEquals(85, $log->similarity_ratio);
        $this->assertEquals(75, $log->min_similarity_ratio);
    }

    public function test_should_invalidate_the_invoice_consumer_name_when_an_empty_string_is_provided(): void
    {
        $this->device->invoice->update(['consumer_name' => '']);
        $this->device->refresh();

        $log = (new DeviceOwnerNameValidationAction($this->device))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(0, $log->similarity_ratio);
        $this->assertEquals(75, $log->min_similarity_ratio);
    }
}
