<?php

namespace Tests\Feature;

use App\Models\AccountDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateAccountTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_account()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/auth/register', [
            "email" => "tester@this.com",
            "firstName" => "test",
            "lastName" => "test",
            "username" => "dammy",
            "referralUsername" => $user->username,
            "phone" => "08099878789",
            "password" => "password"
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas("users", ["email" => "tester@this.com"]);
    }

    public function test_user_account_detail_is_created()
    {
        $user = User::factory()->create();

        $this->post('/api/auth/register', [
            "email" => "tester@this.com",
            "firstName" => "test",
            "lastName" => "test",
            "username" => "dammy",
            "referralUsername" => $user->username,
            "phone" => "08099878789",
            "password" => "password"
        ]);

        $createdUser = User::where("email", "tester@this.com")->first();

        $this->assertDatabaseHas("account_details", ["user_id" => $createdUser->id]);
    }
}
