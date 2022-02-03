<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;
    
    protected function getToken()
    {
        $data = [
            "name" => "Anggoro",
            "email" => "anggoro@mail.com",
            "password" => "password123",
        ];

        $user = User::where('email', $data['email'])->first();
        if ($user) {
            $user->delete();
        }

        $this->post(route('register'), $data);

        $response = $this->post(route('login'), ['email' => $data['email'], 'password' => $data['password']]);

        return $response['access_token'];
    }

    /** @test */
    public function store()
    {
        $data = [
            'title' => 'Bertemu teman',
            'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
            'start' => '2022-02-05 09:30:34',
            'end' => '2022-02-05 11:30:34',
        ];

        $token = $this->getToken();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post(route('todos.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'title',
                'description',
                'status',
                'user_id',
            ],
            'message',
        ]);
    }

    // /** @test */
    public function index()
    {
        $data = [
            'title' => 'Bertemu teman',
            'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
            'status' => '1',
            'user_id' => '1'
        ];

        $token = $this->getToken();

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post(route('todos.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'title',
                'description',
                'status',
                'user_id',
            ],
            'message',
        ]);
    }
}
