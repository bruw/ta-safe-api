<?php

namespace Tests\Feature\Controllers\UserController;

use App\Http\Messages\FlashMessage;
use App\Models\User;

use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private $randomName;
    private $randomEmail;
    private $randomPhone;

    protected function setUp(): void
    {
        parent::SetUp();

        $faker = \Faker\Factory::create('pt_BR');

        $this->user = User::factory()->create();

        $this->randomName = $faker->name();
        $this->randomEmail = $faker->email();
        $this->randomPhone = $faker->cellPhoneNumber();
    }

    public function test_an_unauthenticated_user_is_not_authorized_to_update_profile(): void
    {
        $response = $this->putJson("/api/user");
        
        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_a_user_can_update_their_editable_attributes(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->putJson("/api/user", [
            'name' => $this->randomName,
            'email' => $this->randomEmail,
            'phone' => $this->randomPhone
        ]);

        $response->assertNoContent();

        $this->assertEquals(
            $this->user->name,
            $this->randomName
        );

        $this->assertEquals(
            $this->user->email,
            $this->randomEmail
        );

        $this->assertEquals(
            $this->user->phone,
            $this->randomPhone
        );
    }

    public function test_a_user_can_update_the_name_without_changing_other_attributes(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $userBeforeUpdate = $this->user;

        $response = $this->putJson("/api/user", [
            'name' =>  $this->randomName,
            'email' => $this->user->email,
            'phone' => $this->user->phone
        ]);

        $response->assertNoContent();

        $this->assertEquals(
            $this->user->name,
            $this->randomName
        );

        $this->assertEquals(
            $this->user->email,
            $userBeforeUpdate->email
        );

        $this->assertEquals(
            $this->user->phone,
            $userBeforeUpdate->phone
        );
    }

    public function test_a_user_can_update_the_email_without_changing_other_attributes(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $userBeforeUpdate = $this->user;

        $response = $this->putJson("/api/user", [
            'name' =>  $this->user->name,
            'email' => $this->randomEmail,
            'phone' => $this->user->phone
        ]);

        $response->assertNoContent();

        $this->assertEquals(
            $this->user->name,
            $userBeforeUpdate->name
        );

        $this->assertEquals(
            $this->user->email,
            $this->randomEmail
        );

        $this->assertEquals(
            $this->user->phone,
            $userBeforeUpdate->phone
        );
    }

    public function test_a_user_can_update_the_phone_without_changing_other_attributes(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $userBeforeUpdate = $this->user;

        $response = $this->putJson("/api/user", [
            'name' =>  $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->randomPhone
        ]);

        $response->assertNoContent();

        $this->assertEquals(
            $this->user->name,
            $userBeforeUpdate->name
        );

        $this->assertEquals(
            $this->user->email,
            $userBeforeUpdate->email
        );

        $this->assertEquals(
            $this->user->phone,
            $this->randomPhone
        );
    }

    public function test_prohibit_update_without_params(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $response = $this->putJson("/api/user");

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors([
            'name', 'email', 'phone'
        ]);
    }

    public function test_prohibit_update_with_invalid_name(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $invalidNames = [
            null, "", true, false, rand(0, 200), Str::random(256)
        ];

        foreach ($invalidNames as $invalidName) {
            $response = $this->putJson("/api/user", [
                'name' =>  $invalidName,
                'email' => $this->user->email,
                'phone' => $this->user->phone
            ]);

            $response->assertUnprocessable();

            $response->assertJsonValidationErrors([
                'name'
            ]);
        }
    }

    public function test_prohibit_update_with_invalid_email(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $user2 = User::factory()->create();

        $invalidEmails = [
            null, "", true, false, rand(0, 200), Str::random(256),
            'example.email.com', 'example@.com', 'example@@example.com',
            $user2->email
        ];

        foreach ($invalidEmails as $invalidEmail) {
            $response = $this->putJson("/api/user", [
                'name' =>  $this->user->name,
                'email' => $invalidEmail,
                'phone' => $this->user->phone
            ]);

            $response->assertUnprocessable();

            $response->assertJsonValidationErrors([
                'email'
            ]);
        }
    }

    public function test_prohibit_update_with_invalid_phone(): void
    {
        Sanctum::actingAs(
            $this->user,
            []
        );

        $user2 = User::factory()->create();

        $invalidPhones =  [
            null, "", true, false, Str::random(15), '00) 90000-0001', '(00 90000-0001',
            '(00)90000-0001', '(00) 90000000', '(00) 900000001', '(00) 90000-000',
            '(00) 90000-000a', '(x0) 90000-0001', '(00) 80000-0001',
            $user2->phone
        ];

        foreach ($invalidPhones as $invalidPhone) {
            $response = $this->putJson("/api/user", [
                'name' =>  $this->user->name,
                'email' => $this->user->email,
                'phone' => $invalidPhone
            ]);

            $response->assertUnprocessable();

            $response->assertJsonValidationErrors([
                'phone'
            ]);
        }
    }
}
