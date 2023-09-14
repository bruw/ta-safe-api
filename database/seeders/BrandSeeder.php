<?php

namespace Database\Seeders;

use App\Models\Brand;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/brands.json'));
        $data = json_decode($json);

        foreach ($data as $item) {
            Brand::updateOrCreate([
                'name' => $item->name
            ]);
        }
    }
}
