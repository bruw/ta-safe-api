<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::upsert([
            ['id' => 1, 'name' => 'Apple'],
            ['id' => 2, 'name' => 'Motorola'],
            ['id' => 3, 'name' => 'Samsung'],
            ['id' => 4, 'name' => 'Xiaomi']
        ], ['id', 'name'], ['name']);
    }
}
