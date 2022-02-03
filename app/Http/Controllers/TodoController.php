<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class TodoController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'string',
            'start' => 'date_format:Y-m-d H:i:s',
            'end' => 'date_format:Y-m-d H:i:s|after:start',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        if (request('start') > Carbon::now()) {
            $request['status'] = 'inactive';
        } else {
            if (request('end') < Carbon::now()) {
                $request['status'] = 'completed';

            } else {
                $request['status'] = 'active';
            }
        }

        $request['user_id'] = auth()->user()->id;

        $todo = Todo::create($request->all());

        $response = [
            'data' => [
                'title' => $todo->title,
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
