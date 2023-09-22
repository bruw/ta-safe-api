<?php

namespace Tests\Feature\Factory;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_must_correctly_instantiate_a_brand_without_persisting_in_the_database(): void
    {
        $brand = Brand::factory()->make();

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertModelMissing($brand);

        $this->assertNotNull($brand->name);
    }

    public function test_must_correctly_instantiate_a_brand_and_persist_in_the_database(): void
    {
        $brand = Brand::factory()->create();

        $this->assertInstanceOf(Brand::class, $brand);
        $this->assertModelExists($brand);

        $this->assertNotNull($brand->name);
    }
}
