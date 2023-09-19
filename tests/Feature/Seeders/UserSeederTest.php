<?php

namespace Tests\Feature\Seeders;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UserSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::SetUp();

        $this->seed([
            UserSeeder::class
        ]);
    }

    public function test_must_have_created_the_correct_number_of_users(): void
    {
        $json = File::get(database_path('data/users.json'));
        $data = json_decode($json);

        $this->assertEquals(User::count(), count($data));
    }

    public function test_the_users_attributes_must_have_been_generated_correctly(): void
    {
        $json = File::get(database_path('data/users.json'));
        $data = json_decode($json);

        foreach ($data as $item) {
            $user = User::where(['name' => $item->name])->first();

            $this->assertNotNull($user);

            $this->assertEquals($user->name, $item->name);
            $this->assertEquals($user->email, $item->email);
            $this->assertTrue(password_verify($item->password, $user->password));
            $this->assertEquals($user->cpf, $item->cpf);
            $this->assertEquals($user->phone, $item->phone);
        }
    }
}
