<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function testRequiredFieldsForRegistration()
    {
        $this->json('POST', 'api/register')
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "name" => ["The name field is required."],
                    "email" => ["The email field is required."],
                    "password" => ["The password field is required."],
                ]
            ]);
    }

    public function testRepeatPassword()
    {
        $userData =
            [
                'name' => "ahmed",
                "email" => "admin@admin.com",
                "password" => "123456789"
            ];
        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "password" => ["The password confirmation does not match."]
                ]
            ]);
    }
    public function testUniqueEmail()
    {
        \App\Models\User::factory(1)->create();
        $userData =
            [
                'name' => "ahmed",
                "email" => "admin@admin.com",
                "password" => "123456789",
                "password_confirmation" => "123456789"
            ];
        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                "message" => "The given data was invalid.",
                "errors" => [
                    "email" => ["The email has already been taken."]
                ]
            ]);
    }

    public function testSuccessfulRegistration()
    {
        $userData =
            [
                'name' => "ahmed",
                "email" => "admin@admin.com",
                "password" => "123456789",
                "password_confirmation" => "123456789"
            ];
        $this->json('POST', 'api/register', $userData, ['Accept' => 'application/json'])
            ->assertStatus(201)->assertJsonStructure(
                ['data' => [
                    'id',
                    'name',
                    'email'
                ], "access_token"]
            );
    }
    public function testSuccessfulLogin()
    {
        \App\Models\User::factory(1)->create();
        $userData =
            [

                "email" => "admin@admin.com",
                "password" => "123456789",

            ];
        $this->json('POST', 'api/login', $userData, ['Accept' => 'application/json'])
            ->assertStatus(200)->assertJsonStructure(
                ['data' => [
                    'id',
                    'name',
                    'email'
                ], "access_token"]
            );
    }
}
