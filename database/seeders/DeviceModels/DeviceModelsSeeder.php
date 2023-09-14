<?php

namespace Database\Seeders\DeviceModels;

use Illuminate\Database\Seeder;

class DeviceModelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            Edge20Seeder::class,
            Edge30Seeder::class,
            GalaxyS20Seeder::class,
            GalaxyS21Seeder::class,
            GalaxyS22Seeder::class,
            Iphone11Seeder::class,
            Iphone12Seeder::class,
            Iphone13Seeder::class,
            PocoX3ProSeeder::class,
            PocoX4ProSeeder::class
        ]);
    }
}
