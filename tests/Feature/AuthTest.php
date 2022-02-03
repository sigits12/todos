<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // use RefreshDatabase;

    protected function deleteUser($data)
    {
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $user->delete();
        }
    }

    /** @test */
    public function register()
    {
        $data = [
            "name" => "Anggoro",
            "email" => "anggoro@mail.com",
            "password" => "password123",
        ];

        $this->deleteUser($data);

        $response = $this->post(route('register'), $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'name',
                'email',
            ],
            'access_token',
            'token_type'
        ]);
    }

    /** @test */
    public function login()
    {
        $data = [
            "name" => "Anggoro",
            "email" => "anggoro@mail.com",
            "password" => "password123",
        ];

        $this->deleteUser($data);

        $this->register();

        $response = $this->post(route('login'), $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'name',
                'email',
            ],
            'access_token',
            'token_type'
        ]);
    }
}
