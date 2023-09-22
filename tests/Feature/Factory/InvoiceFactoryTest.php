<?php

namespace Tests\Feature\Factory;

use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceFactoryTest extends TestCase
{
    use RefreshDatabase;

    private Device $device;

    protected function setUp(): void
    {
        parent::SetUp();

        $deviceModel = DeviceModel::factory()
            ->for(Brand::factory())
            ->create();

        $this->device = Device::factory()
            ->for(User::factory())
            ->for($deviceModel)
            ->create();
    }

    public function test_must_correctly_instantiate_a_invoice_without_persisting_in_the_database(): void
    {
        $invoice = Invoice::factory()
            ->for($this->device)
            ->make();

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertModelMissing($invoice);

        $this->assertNotNull($invoice->access_key);
    }

    public function test_must_correctly_instantiate_a_invoice_and_persist_in_the_database(): void
    {
        $invoice = Invoice::factory()
            ->for($this->device)
            ->create();

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertModelExists($invoice);

        $this->assertNotNull($invoice->access_key);
    }
}
