<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\Users;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;

// use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    //

    public function get(Request $request)
    {
        $query = Users::when($request->filled(['search']), function ($query) {
            $keyword = trim(request('search'));
            return $query->where(function ($query) use ($keyword) {
                $query->where('id', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%');
            });
        });

        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy === "name") {
                    $query->orderBy('name_tc', $sortDesc ? 'DESC' : 'ASC');
                    $query->orderBy('name_en', $sortDesc ? 'DESC' : 'ASC');
                } else {
                    if ($sortBy !== "action") {
                        $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
                    }
                }
                $index++;
            }
        }

        $response = config('response.common.success');
        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
            $resource = new UserCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new UserCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:users,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $result = Users::where(['id' => request('id')])->first();
        $resource = new UserResource($result);

        $response = config('response.common.success');
        $response['data'] = $resource;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|max:255',
            'name' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|string|in:admin,employee',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            Users::create([
                'id' => Common::userId(),
                'name' => request('name'),
                'phone' => request('phone'),
                'email' => request('email'),
                'password' => Hash::make(request('password')),
                'role' => request('role'),
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:users,id',
            'name' => 'required|string',
            'role' => 'required|string|in:admin,employee',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            Users::where(['id' => request('id')])
                ->update([
                    'name' => request('name'),
                    'role' => request('role'),
                ]);

            $user = Users::where(['phone' => request('phone')])->first();
            if (!$user) {
                Users::where(['id' => request('id')])
                    ->update([
                        'phone' => request('phone'),
                    ]);
            }

            $user = Users::where(['email' => request('email')])->first();
            if (!$user) {
                Users::where(['id' => request('id')])
                    ->update([
                        'email' => request('email'),
                    ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        $response['data'] = Users::where(['id' => request('id')])->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:users,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            Users::where(['id' => request('id')])->delete();
            // $user->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        return response()->json($response, 200);
    }

    public function export(Request $request)
    {
        $query = Users::when($request->filled(['search']), function ($query) {
            $keyword = trim(request('search'));
            return $query->where(function ($query) use ($keyword) {
                $query->where('id', 'like', '%' . $keyword . '%')
                    ->orWhere('name', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%');
            });
        });

        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy === "name") {
                    $query->orderBy('name_tc', $sortDesc ? 'DESC' : 'ASC');
                    $query->orderBy('name_en', $sortDesc ? 'DESC' : 'ASC');
                } else {
                    if ($sortBy !== "action") {
                        $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
                    }
                }
                $index++;
            }
        }

        $result = $query->get();
        $headers = null;
        $rows = [];
        foreach ($result->toArray() as $item) {
            [$keys, $values] = Arr::divide($item);
            $headers = $keys;
            $rows[] = $values;
        }

        $path = MyPhpOffice::exportTableWithPath('user', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }

}