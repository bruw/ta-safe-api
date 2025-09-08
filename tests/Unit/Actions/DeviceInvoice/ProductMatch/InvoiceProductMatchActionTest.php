<?php

namespace Tests\Unit\Actions\DeviceInvoice\ProductMatch;

use App\Actions\DeviceInvoice\ProductMatch\InvoiceProductMatchAction;
use App\Dto\Invoice\Search\InvoiceProductMatchResultDto;

class InvoiceProductMatchActionTest extends InvoiceProductMatchActionTestSetUp
{
    public function test_should_return_an_instance_of_the_expected_dto(): void
    {
        $result = (new InvoiceProductMatchAction($this->device))->execute();
        $this->assertInstanceOf(InvoiceProductMatchResultDto::class, $result);
    }

    public function test_the_dto_should_contains_the_expected_attributes(): void
    {
        $result = (new InvoiceProductMatchAction($this->device))->execute();

        $this->assertObjectHasProperty('product', $result);
        $this->assertObjectHasProperty('similarityScore', $result);
    }

    public function test_should_extract_the_expected_device_in_the_product_list_in_the_device_invoice(): void
    {
        $result = (new InvoiceProductMatchAction($this->device))->execute();
        $this->assertEquals($result->product, $this->deviceDescription());
    }

    public function test_should_return_the_maximum_similarity_score_of_1400_points_when_it_is_a_perfect_match(): void
    {
        $result = (new InvoiceProductMatchAction($this->device))->execute();
        $this->assertEquals(1400, $result->similarityScore);
    }

    public function test_should_extract_the_expected_device_description_from_ambiguous_description(): void
    {
        $this->device->invoice->update([
            'product_description' => $this->device->invoice->product_description
                . $this->ambiguousInvoiceDescription(),
        ]);

        $result = (new InvoiceProductMatchAction($this->device))->execute();
        $this->assertEquals($result->product, $this->deviceDescription());
    }

    public function test_should_extract_the_expected_device_description_from_the_list_of_similar_devices(): void
    {
        $this->device->invoice->update([
            'product_description' => $this->device->invoice->product_description
                . '<span> Apple iPhone 12 8gb 128gb preto </span>'
                . '<span> Xiaomi Poco x5 pro 8gb 256gb preto </span>'
                . '<span> Samsung Galaxy M52 5g Dual Sim 128 Gb Black 6 Gb Ram </span>',
        ]);

        $result = (new InvoiceProductMatchAction($this->device))->execute();
        $this->assertEquals($result->product, $this->deviceDescription());
    }

    public function test_should_return_an_empty_product_and_a_score_0_when_the_invoice_product_description_is_empty(): void
    {
        $this->device->invoice->update(['product_description' => '']);

        $result = (new InvoiceProductMatchAction($this->device))->execute();

        $this->assertEquals('', $result->product);
        $this->assertEquals(0, $result->similarityScore);
    }
}
