<?php

namespace Tests\Unit\Actions\DeviceInvoice\Validation\User\Cpf;

use App\Actions\DeviceInvoice\Validation\User\Cpf\DeviceOwnerCpfValidationAction;
use App\Models\DeviceAttributeValidationLog;
use App\Models\User;

class DeviceOwnerCpfValidationActionTest extends DeviceOwnerCpfValidationActionTestSetUp
{
    public function test_should_return_an_instance_of_device_validation_log(): void
    {
        $log = (new DeviceOwnerCpfValidationAction($this->device))->execute();
        $this->assertInstanceOf(DeviceAttributeValidationLog::class, $log);
    }

    public function test_a_validation_log_record_should_be_written_to_the_database_when_the_action_is_successful(): void
    {
        (new DeviceOwnerCpfValidationAction($this->device))->execute();

        $this->assertDatabaseHas('device_attribute_validation_logs', [
            'user_id' => $this->device->user->id,
            'device_id' => $this->device->id,
            'attribute_source' => User::class,
            'attribute_label' => 'cpf',
            'attribute_value' => $this->user->cpf,
            'invoice_attribute_label' => 'consumer_cpf',
            'invoice_attribute_value' => $this->device->invoice->consumer_cpf,
            'similarity_ratio' => 100,
            'min_similarity_ratio' => 100,
            'validated' => true,
        ]);
    }

    public function test_identical_cpf_values_should_return_a_similarity_score_of_100(): void
    {
        $log = (new DeviceOwnerCpfValidationAction($this->device))->execute();

        $this->assertEquals(100, $log->similarity_ratio);
        $this->assertTrue($log->validated);
    }

    public function test_the_action_must_not_be_able_to_validate_a_cpf_with_missing_digits(): void
    {
        $this->invoiceUpdate(cpf: substr($this->user->cpf, 0, -1));
        $log = (new DeviceOwnerCpfValidationAction($this->device))->execute();

        $this->assertFalse($log->validated);
    }

    public function test_the_action_must_not_be_able_to_validate_a_cpf_with_extra_digits(): void
    {
        $this->invoiceUpdate(cpf: $this->user->cpf . 0);
        $log = (new DeviceOwnerCpfValidationAction($this->device))->execute();

        $this->assertFalse($log->validated);
    }

    public function test_the_action_must_not_be_able_to_validate_a_cpf_that_is_opposite_of_the_reference_value(): void
    {
        $this->invoiceUpdate(cpf: strrev($this->user->cpf));
        $log = (new DeviceOwnerCpfValidationAction($this->device))->execute();

        $this->assertFalse($log->validated);
    }

    public function test_should_invalidate_the_invoice_consumer_cpf_when_an_empty_string_is_provided(): void
    {
        $this->invoiceUpdate(cpf: '');
        $log = (new DeviceOwnerCpfValidationAction($this->device))->execute();

        $this->assertFalse($log->validated);
        $this->assertEquals(0, $log->similarity_ratio);
        $this->assertEquals(100, $log->min_similarity_ratio);
    }
}
