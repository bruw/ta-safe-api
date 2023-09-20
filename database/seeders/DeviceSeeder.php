<?php

namespace Database\Seeders;

use App\Enums\Device\DeviceValidationStatus;
use App\Models\Device;
use App\Models\DeviceModel;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/devices.json'));
        $data = json_decode($json);

        foreach ($data as $item) {
            $user = User::where([
                'name' => $item->user->name,
                'cpf' => $item->user->cpf
            ])->first();

            $deviceModel = DeviceModel::where([
                'name' => $item->model->name,
                'ram' => $item->model->ram,
                'storage' => $item->model->storage
            ])->first();

            $device = Device::updateOrCreate([
                'color' => $item->color,
                'imei1' => $item->imei1,
                'imei2' => $item->imei2,
                'validation_status' => DeviceValidationStatus::VALIDATED,
                'user_id' => $user->id,
                'device_model_id' => $deviceModel->id,
            ]);

            Invoice::updateOrCreate([
                'access_key' => $item->invoice->access_key,
                'consumer_cpf' => $user->cpf,
                'consumer_name' => $user->name,
                'product_description' => $item->invoice->product_description,
                'device_id' => $device->id
            ]);
        }
    }
}
