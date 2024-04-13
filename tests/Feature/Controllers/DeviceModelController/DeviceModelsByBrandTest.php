<?php

namespace Tests\Feature\Controllers\DeviceModelController;

use App\Http\Messages\FlashMessage;
use App\Models\Brand;
use App\Models\DeviceModel;
use Database\Factories\BrandFactory;
use Database\Factories\DeviceModelFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceModelsByBrandTest extends TestCase
{
    use RefreshDatabase;

    private Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->brand = BrandFactory::new()->create();

        DeviceModelFactory::new()
            ->for($this->brand)
            ->count(10)
            ->create();
    }

    public function test_an_unauthenticated_user_should_not_be_authorized_to_obtain_all_device_models_by_brand(): void
    {
        $response = $this->getJson("api/device-models/brands/{$this->brand->id}");

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_an_authenticated_user_must_be_authorized_to_obtain_all_device_models_by_brand(): void
    {
        Sanctum::actingAs(UserFactory::new()->create());

        $response = $this->getJson("api/device-models/brands/{$this->brand->id}");

        $deviceModel = DeviceModel::first();

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->has(
                DeviceModel::all()->count()
            )->first(
                fn (AssertableJson $json) => $json->where('id', $deviceModel->id)
                    ->where('name', $deviceModel->name)
                    ->where('ram', $deviceModel->ram)
                    ->where('storage', $deviceModel->storage)
                    ->where('brand.name', $deviceModel->brand->name)
            )
        );
    }

    public function test_should_return_an_error_when_the_brand_id_param_does_not_exists(): void
    {
        Sanctum::actingAs(UserFactory::new()->create());

        $latestBrand = Brand::latest('id')->first();
        $invalidId = $latestBrand->id + 1;

        $response = $this->getJson("api/device-models/brands/{$invalidId}");

        $response->assertNotFound()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.not_found'))
        );
    }
}
