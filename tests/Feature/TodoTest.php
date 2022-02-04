<?php

namespace Tests\Feature;

use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTest extends TestCase
{
    use RefreshDatabase;


    protected function bulkRegister()
    {
        $data = [
            [
                "name" => "Anggoro",
                "email" => "anggoro@mail.com",
                "password" => "password123",
            ], [
                "name" => "Anggoro1",
                "email" => "anggoro1@mail.com",
                "password" => "password123",
            ]
        ];

        foreach ($data as $value) {
            $this->post(route('register'), $value);
        }

        return $data;
    }

    protected function getToken($data)
    {
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

        $user = $this->bulkRegister()[0];

        $token = $this->getToken($user);

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

    /** @test */
    public function index_with_filter_by_user()
    {
        $data = [
            [
                'title' => 'Bertemu teman',
                'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
                'start' => '2022-02-05 09:30:34',
                'end' => '2022-02-05 11:30:34',
                'user_id' => '1',
                'status' => 'inactive',
            ],
            [
                'title' => 'Bertemu teman',
                'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
                'start' => '2022-02-05 09:30:34',
                'end' => '2022-02-05 11:30:34',
                'user_id' => '1',
                'status' => 'inactive',
            ],
            [
                'title' => 'Bertemu teman',
                'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
                'start' => '2022-02-05 09:30:34',
                'end' => '2022-02-05 11:30:34',
                'user_id' => '2',
                'status' => 'inactive',
            ]
        ];

        $user = $this->bulkRegister()[0];

        $token = $this->getToken($user);

        Todo::insert($data);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get(route('todos.index'));

        $this->assertCount(2, $response->json(['data']));
    }

    /** @test */
    public function index_with_filter_by_status()
    {
        $data = [
            [
                'title' => 'Bertemu teman',
                'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
                'start' => '2022-02-04 06:30:34',
                'end' => '2022-02-04 11:30:34',
                'user_id' => '1',
                'status' => 'active',
            ],
            [
                'title' => 'Bertemu teman',
                'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
                'start' => '2022-02-02 09:30:34',
                'end' => '2022-02-02 11:30:34',
                'user_id' => '1',
                'status' => 'completed',
            ],
            [
                'title' => 'Bertemu teman',
                'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
                'start' => '2022-02-05 09:30:34',
                'end' => '2022-02-05 11:30:34',
                'user_id' => '2',
                'status' => 'inactive',
            ]
        ];

        $user = $this->bulkRegister()[0];

        $token = $this->getToken($user);

        Todo::insert($data);

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->get(route('todos.index',  ['status' => 'completed']));

        $this->assertCount(1, $response->json(['data']));
    }

    /** @test */
    public function update()
    {
        $data = [
            'title' => 'Bertemu teman',
            'description' => 'Di tempat futsal biasa, membawa tas yang hijau',
            'start' => '2022-02-04 06:30:34',
            'end' => '2022-02-04 11:30:34',
            'user_id' => '1',
            'status' => 'active',
        ];

        $user = $this->bulkRegister()[0];

        $token = $this->getToken($user);

        $todo = Todo::create($data);

        $new = [
            'title' => 'Bertemu teman sekolah',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->patch(route('todos.update',  ['todo' => $todo->id]), $new);
        
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => $new['title']
                ],
            ]);
    }
}
