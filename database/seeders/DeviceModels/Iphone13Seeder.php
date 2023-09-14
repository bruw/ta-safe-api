<?php

namespace Database\Seeders\DeviceModels;

use App\Models\Brand;
use App\Models\DeviceModel;
use Illuminate\Database\Seeder;

class Iphone13Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brand = Brand::where(['name' => 'Apple'])->firstOrFail();

        $this->seedIphone128Gb($brand);
        $this->seedIphone256Gb($brand);
        $this->seedIphone512Gb($brand);
    }

    /**
     * Run seed for iphone 13 - 128 gb.
     * https://support.apple.com/kb/SP851?viewlocale=pt_BR&locale=pt_BR
     * 
     * @param \App\Models\Brand $brand
     * @return void
     */
    private function seedIphone128Gb(Brand $brand): void
    {
        DeviceModel::updateOrCreate([
            'name' => 'Iphone 13',
            'chipset' => 'Apple A15 Bionic',
            'ram' => '4 GB',
            'storage' => '128 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '1170 x 2532',
            'battery_capacity' => '3240',
            'year_of_manufacture' => '2021',
            'os' => 'IOS',
            'brand_id' => $brand->id
        ]);
    }

    /**
     * Run seed for iphone 13 - 256 gb.
     * https://support.apple.com/kb/SP851?viewlocale=pt_BR&locale=pt_BR
     * 
     * @param \App\Models\Brand $brand
     * @return void
     */
    private function seedIphone256Gb(Brand $brand): void
    {
        DeviceModel::updateOrCreate([
            'name' => 'Iphone 13',
            'chipset' => 'Apple A15 Bionic',
            'ram' => '4 GB',
            'storage' => '256 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '1170 x 2532',
            'battery_capacity' => '3240',
            'year_of_manufacture' => '2021',
            'os' => 'IOS',
            'brand_id' => $brand->id
        ]);
    }

    /**
     * Run seed for iphone 13 - 512 gb.
     * https://support.apple.com/kb/SP851?viewlocale=pt_BR&locale=pt_BR
     * 
     * @param \App\Models\Brand $brand
     * @return void
     */
    private function seedIphone512Gb(Brand $brand): void
    {
        DeviceModel::updateOrCreate([
            'name' => 'Iphone 13',
            'chipset' => 'Apple A15 Bionic',
            'ram' => '4 GB',
            'storage' => '512 GB',
            'screen_size' => '6.1',
            'screen_resolution' => '1170 x 2532',
            'battery_capacity' => '3240',
            'year_of_manufacture' => '2021',
            'os' => 'IOS',
            'brand_id' => $brand->id
        ]);
    }
}
