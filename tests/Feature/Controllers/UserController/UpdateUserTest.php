<?php

namespace Tests\Feature\Controllers\UserController;

use App\Http\Messages\FlashMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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
        $response = $this->putJson('/api/user');

        $response->assertUnauthorized()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('http_exceptions.unauthenticated'))
        );
    }

    public function test_a_user_can_update_their_editable_attributes(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/user', [
            'name' => $this->randomName,
            'email' => $this->randomEmail,
            'phone' => $this->randomPhone,
        ]);

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.updated.m', 1, [
                    'model' => trans_choice('model.profile', 1),
                ]))
        );

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
        Sanctum::actingAs($this->user);

        $userBeforeUpdate = $this->user;

        $response = $this->putJson('/api/user', [
            'name' => $this->randomName,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ]);

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.updated.m', 1, [
                    'model' => trans_choice('model.profile', 1),
                ]))
        );

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
        Sanctum::actingAs($this->user);

        $userBeforeUpdate = $this->user;

        $response = $this->putJson('/api/user', [
            'name' => $this->user->name,
            'email' => $this->randomEmail,
            'phone' => $this->user->phone,
        ]);

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.updated.m', 1, [
                    'model' => trans_choice('model.profile', 1),
                ]))
        );

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
        Sanctum::actingAs($this->user);

        $userBeforeUpdate = $this->user;

        $response = $this->putJson('/api/user', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->randomPhone,
        ]);

        $response->assertOk()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::SUCCESS)
                ->where('message.text', trans_choice('flash_messages.success.updated.m', 1, [
                    'model' => trans_choice('model.profile', 1),
                ]))
        );

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
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/user');

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.name.0', trans('validation.required', [
                    'attribute' => trans('validation.attributes.name'),
                ]))
                ->where('errors.email.0', trans('validation.required', [
                    'attribute' => 'email',
                ]))
                ->where('errors.phone.0', trans('validation.required', [
                    'attribute' => trans('validation.attributes.phone'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_name_param_is_null(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/user', [
            'name' => null,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.name.0', trans('validation.required', [
                    'attribute' => trans('validation.attributes.name'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_name_param_is_longer_than_255_characters(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/user', [
            'name' => Str::random(256),
            'email' => $this->user->email,
            'phone' => $this->user->phone,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.name.0', trans('validation.max.string', [
                    'max' => 255,
                    'attribute' => trans('validation.attributes.name'),
                ]))
        );
    }

    public function test_should_return_an_error_when_the_email_param_is_null(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/user', [
            'name' => $this->user->name,
            'email' => null,
            'phone' => $this->user->phone,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.email.0', trans('validation.required', [
                    'attribute' => 'email',
                ]))
        );
    }

    public function test_should_return_an_error_when_the_email_param_is_longer_than_255_characters(): void
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/user', [
            'name' => $this->user->name,
            'email' => Str::random(256),
            'phone' => $this->user->phone,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.email.0', trans('validation.email', [
                    'attribute' => 'email',
                ]))
                ->where('errors.email.1', trans('validation.max.string', [
                    'max' => 255,
                    'attribute' => 'email',
                ]))
        );
    }

    public function test_should_return_an_error_when_the_email_param_is_not_a_valid_email_format(): void
    {
        Sanctum::actingAs($this->user);

        $invalidEmails = [
            'example.email.com', 'example@.com', 'example@@example.com',
        ];

        foreach ($invalidEmails as $email) {
            $response = $this->putJson('/api/user', [
                'name' => $this->user->name,
                'email' => $email,
                'phone' => $this->user->phone,
            ]);

            $response->assertUnprocessable()->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.email.0', trans('validation.email', [
                        'attribute' => 'email',
                    ]))
                    ->etc()
            );
        }
    }

    public function test_should_return_an_error_when_the_email_param_is_already_in_use_by_another_user(): void
    {
        Sanctum::actingAs($this->user);

        $user2 = User::factory()->create();

        $response = $this->putJson('/api/user', [
            'name' => $this->user->name,
            'email' => $user2->email,
            'phone' => $this->user->phone,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.email.0', trans('validation.unique', [
                    'attribute' => 'email',
                ]))
        );
    }

    public function test_prohibit_update_with_invalid_phone_format(): void
    {
        Sanctum::actingAs($this->user);

        $invalidPhones = [
            '00) 90000-0001', '(00 90000-0001',
            '(00)90000-0001', '(00) 90000000', '(00) 900000001', '(00) 90000-000',
            '(00) 90000-000a', '(x0) 90000-0001',
        ];

        foreach ($invalidPhones as $invalidPhone) {
            $response = $this->putJson('/api/user', [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $invalidPhone,
            ]);

            $response->assertUnprocessable()->assertJson(
                fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                    ->where('message.text', trans('flash_messages.errors'))
                    ->where('errors.phone.0', trans('validation.regex', [
                        'attribute' => trans('validation.attributes.phone'),
                    ]))
            );
        }
    }

    public function test_should_return_an_error_when_the_phone_param_is_already_in_use_by_another_user(): void
    {
        Sanctum::actingAs($this->user);

        $user2 = User::factory()->create();

        $response = $this->putJson('/api/user', [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $user2->phone,
        ]);

        $response->assertUnprocessable()->assertJson(
            fn (AssertableJson $json) => $json->where('message.type', FlashMessage::ERROR)
                ->where('message.text', trans('flash_messages.errors'))
                ->where('errors.phone.0', trans('validation.unique', [
                    'attribute' => trans('validation.attributes.phone'),
                ]))
        );
    }
}
