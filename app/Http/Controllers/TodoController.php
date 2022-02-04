<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => [
                Rule::in(['inactive', 'active', 'completed']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        $todos = Todo::byUser()->when($request->has('status'), function ($query) use ($request) {
            $query->where('status', 'LIKE', '%' . $request->status . '%');
        })->get();

        $response = [
            'data' => $todos,
            'message' => 'success',
        ];

        return response()
            ->json($response, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'string',
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s|after:start',
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

    public function update(Request $request, $todo)
    {
        $arrValidation = [];

        foreach ($request->all() as $key => $value) {
            switch ($key) {
                case 'title':
                    $arrValidation['title'] = 'required|string|max:255';
                    break;
                case 'description':
                    $arrValidation['description'] = 'string|nullable';
                    break;
                case 'start':
                    $arrValidation['start'] = 'required|date_format:Y-m-d H:i:s';
                    break;
                case 'end':
                    $arrValidation['end'] = 'required|date_format:Y-m-d H:i:s|after:start';
                    break;
                default:
                    break;
            }
        }

        $validator = Validator::make($request->all(), $arrValidation);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $todo = Todo::findOrFail($todo);
        } catch (ModelNotFoundException $e) {
            return $this->notFound();
        }
        $todo->update($request->all());

        $response = [
            'data' => [
                'title' => $todo->title,
                'description' => $todo->description,
                'status' => $todo->status,
                'user_id' => $todo->user_id,
            ],
            'message' => 'Update Data Success',
        ];

        return response()
            ->json($response, 200);
    }

    public function updateStatus(Request $request, $todo)
    {
        $validator = Validator::make($request->all(), [
            'status' => [
                Rule::in(['inactive', 'active', 'completed']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }

        try {
            $todo = Todo::findOrFail($todo);
        } catch (ModelNotFoundException $e) {
            return $this->notFound();
        }

        $todo->status = $request->status;
        $todo->save();

        $response = [
            'data' => [
                'title' => $todo->title,
                'description' => $todo->description,
                'status' => $todo->status,
                'user_id' => $todo->user_id,
            ],
            'message' => 'Update Status Success',
        ];

        return response()
            ->json($response, 200);
    }

    public function destroy($todo)
    {
        try {
            $todo = Todo::findOrFail($todo);
        } catch (ModelNotFoundException $e) {
            return $this->notFound();
        }
        $todo->delete();

        $response = [
            'message' => 'Deleted Success',
        ];

        return response()
            ->json($response, 200);
    }
}
