<?php

namespace Tests\Unit\Actions\DeviceOwnerInvoiceValidation;

use App\Actions\DeviceOwnerInvoiceValidation\FindProductInInvoiceMatchingDeviceAction;
use App\Models\Brand;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Traits\StringNormalizer;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\InvoiceFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindProductInInvoiceMatchingDeviceTest extends TestCase
{
    use RefreshDatabase;
    use StringNormalizer;

    private Brand $brand;
    private Device $device;
    private DeviceModel $deviceModel;
    private Invoice $invoice;

    protected function setUp(): void
    {
        parent::setUp();

        $this->brandSetUp();
        $this->deviceSetUp();
    }

    /*
    ================= **START OF SETUP** ==========================================================================
    */

    private function brandSetUp(): void
    {
        $this->brand = BrandFactory::new()->create(['name' => 'Apple']);
    }

    private function deviceSetUp(): void
    {
        $this->deviceModel = DeviceModelFactory::new()
            ->for($this->brand)
            ->create([
                'name' => 'iPhone 11',
                'ram' => '128',
                'storage' => '8',
            ]);

        $this->device = DeviceFactory::new()
            ->for(UserFactory::new()->create())
            ->for($this->deviceModel)
            ->create([
                'color' => 'preto',
            ]);

        $this->invoice = InvoiceFactory::new()
            ->for($this->device)
            ->create();
    }

    /*
     ================= **START OF TESTS** ==========================================================================
    */

    public function test_must_return_the_product_description_when_the_score_is_greather_or_equal_to_650(): void
    {
        $description = 'Smartphone Apple iPhone 11';

        $this->invoice->update([
            'product_description' => "<span>{$description}</span>",
        ]);

        $action = new FindProductInInvoiceMatchingDeviceAction($this->device);
        $result = $action->execute();

        $this->assertEquals($result['product'], $description);
        $this->assertEquals($result['score'], 525);
    }

    public function test_should_return_null_when_the_similarity_score_is_less_than_650(): void
    {
        $description = 'Apple';

        $this->invoice->update([
            'product_description' => "<span>{$description}</span>",
        ]);

        $action = new FindProductInInvoiceMatchingDeviceAction($this->device);
        $result = $action->execute();

        $this->assertNull($result);
    }

    public function test_must_return_null_if_the_invoice_does_not_contain_products(): void
    {
        $this->invoice->update([
            'product_description' => '',
        ]);

        $action = new FindProductInInvoiceMatchingDeviceAction($this->device);
        $result = $action->execute();

        $this->assertNull($result);
    }

    public function test_should_return_the_description_with_the_greatest_similarity_to_the_device_in_a_diverse_list(): void
    {
        $description = 'iPhone 11 128GB';

        $this->invoice->update([
            'product_description' => '<span>calca Sarja Liso Jogger Cargo Caribe HKM BS_FW21_CAL_0013B:P:Stretch Limo</span>'
                . "<span>{$description}</span>"
                . '<span>Camiseta manga longa Meia Malha Liso de algodao peruano</span>'
                . '<span>Blusao Moleton Liso Basico Canguru com Capuz BS__BLU_0005:P:Preto</span>',
        ]);

        $action = new FindProductInInvoiceMatchingDeviceAction($this->device);
        $result = $action->execute();

        $this->assertEquals($result['product'], $description);
    }

    public function test_must_return_the_description_with_the_greatest_similarity_to_the_device_in_a_list_of_similar_products(): void
    {
        $description = 'iPhone 11 128GB';

        $this->invoice->update([
            'product_description' => '<span>Apple Iphone 13</span>'
                . "<span>{$description}</span>"
                . '<span>Apple iPhone 12 128gb</span>'
                . '<span>Capa Apple Iphone 11</span>'
                . '<span>Apple Iphone 11</span>'
                . '<span>Carregador para Iphone 11 preto</span>',
        ]);

        $action = new FindProductInInvoiceMatchingDeviceAction($this->device);
        $result = $action->execute();

        $this->assertEquals($result['product'], $description);
    }

    public function test_should_return_the_description_with_the_greatest_similarity_to_the_device_in_a_list_off_different_devices(): void
    {
        $this->brand->update([
            'name' => 'Samsung',
        ]);

        $this->deviceModel->update([
            'name' => 'Galaxy m52',
        ]);

        $description = 'Samsung Galaxy M52 5g Dual Sim 128 Gb Black 6 Gb Ram';

        $this->invoice->update([
            'product_description' => '<span>Apple Iphone 13</span>'
                . '<span>Poco x5 Pro</span>'
                . '<span>Apple iPhone 12 128gb</span>'
                . '<span>Samsung Galaxy S23 preto</span>'
                . "<span>{$description}</span>",
        ]);

        $action = new FindProductInInvoiceMatchingDeviceAction($this->device);
        $result = $action->execute();

        $this->assertEquals($result['product'], $description);
    }
}
