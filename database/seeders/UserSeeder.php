<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get(database_path('data/users.json'));
        $data = json_decode($json);

        foreach ($data as $item) {
            User::updateOrCreate([
                'name' => $item->name,
                'email' => $item->email,
                'cpf' => $item->cpf,
                'phone' => $item->phone,
                'password' => Hash::make($item->password),
            ]);
        }
    }
}
