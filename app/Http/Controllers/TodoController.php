<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $request['user_id'] = auth()->user()->id;

        $todo = Todo::create($request->all());
        
        $response = [
            'data' => [
                'name' => $todo->name,
                'description' => $todo->description,
                'status' => $todo->status,
                'user_id' => $todo->user_id,
            ],
            'message' => 'success',
        ];

        return response()
            ->json($response, 201);
    }
}
