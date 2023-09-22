<?php

namespace Tests\Feature\Seeders;

use App\Models\Brand;
use Database\Seeders\BrandSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BrandSeederTest extends TestCase
{
    use RefreshDatabase;

    private array $data;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->seed([
            BrandSeeder::class
        ]);

        $json = File::get(database_path('data/brands.json'));
        $this->data = json_decode($json);
    }

    public function test_must_have_created_the_correct_number_of_brands(): void
    {
        $this->assertEquals(Brand::count(), count($this->data));
    }

    public function test_the_brands_attributes_must_have_been_generated_correctly(): void
    {
        foreach ($this->data as $item) {
            $brand = Brand::where(['name' => $item->name])->first();

            $this->assertNotNull($brand);
            $this->assertEquals($brand->name, $item->name);
        }
    }
}
