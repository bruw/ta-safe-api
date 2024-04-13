<?php

namespace Tests\Feature\Controllers\BrandController;

use App\Http\Messages\FlashMessage;
use App\Models\Brand;
use Database\Factories\UserFactory;
use Database\Seeders\BrandSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ViewAllBrandsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BrandSeeder::class);
    }

    public function test_an_unauthenticated_user_should_not_be_authorized_to_view_cell_phone_brands(): void
    {
        $response = $this->getJson('api/brands');

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_an_authenticated_user_must_be_authorized_to_view_cell_phone_brands(): void
    {
        Sanctum::actingAs(UserFactory::new()->create());

        $response = $this->getJson('api/brands');

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->has(
                Brand::all()->count()
            )->first(
                fn (AssertableJson $json) => $json->where('name', Brand::first()->name)
            )
        );
    }
}
