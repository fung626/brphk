<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyCollection;
use App\Models\Company;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class CompanyController extends Controller
{
    //

    public function get(Request $request)
    {

        $user = Auth::user();

        $query = Company::with(['owner'])
            ->select('company.*')
            ->when($request->filled(['owner_user_id']), function ($query) {
                return $query->where('owner_user_id', request('owner_user_id'));
            })
            ->when($user->role === "employee", function ($query) use ($user) {
                return $query->where('owner_user_id', $user->id);
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('name_tc', 'like', '%' . $keyword . '%')
                        ->orWhere('name_en', 'like', '%' . $keyword . '%')
                        ->orWhere('address', 'like', '%' . $keyword . '%')
                        ->orWhere('secretary', 'like', '%' . $keyword . '%');
                });
            })
            ->leftJoin('users as owner', 'owner.id', '=', 'company.owner_user_id')
            ->groupBy('company.id');

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
            $resource = new CompanyCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new CompanyCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:company,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $result = Company::with(['owner'])->where(['id' => request('id')])->first();
        $response = config('response.common.success');
        $response['data'] = $result;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'owner' => 'required',
            'name_en' => 'required|string',
            'name_tc' => 'required|string',
            'number' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }
        $user = Auth::user();
        // request('owner')

        try {
            Company::create([
                'owner_user_id' => request('owner')['id'],
                'user_id' => $user->id,
                'name_en' => request('name_en'),
                'name_tc' => request('name_tc'),
                'number' => request('number'),
                'secretary' => request('secretary'),
                'incorporation_date' => request('incorporation_date'),
                'address' => request('address'),
                'registered_share_capital' => request('registered_share_capital'),
                'share_holders' => is_array(request('share_holders')) ? request('share_holders') : explode(',', request('share_holders')),
                'directors' => is_array(request('directors')) ? equest('directors') : explode(',', request('directors')),
                'updated_by' => $user->id,
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
            'id' => 'required|string|exists:company,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        $result = Company::where(['id' => request('id'), 'user_id' => $user->id])->first();
        if ($user->role !== "admin" && !$result) {
            $response = config('response.common.fail.unauthorised');
            return response()->json($response, 401);
        }

        try {
            Company::where(['id' => request('id')])
                ->update([
                    'owner_user_id' => request('owner')['id'],
                    'name_tc' => request('name_tc'),
                    'name_en' => request('name_en'),
                    'number' => request('number'),
                    'secretary' => request('secretary'),
                    'incorporation_date' => request('incorporation_date'),
                    'address' => request('address'),
                    'registered_share_capital' => request('registered_share_capital'),
                    'share_holders' => is_array(request('share_holders')) ? request('share_holders') : explode(',', request('share_holders')),
                    'directors' => is_array(request('directors')) ? request('directors') : explode(',', request('directors')),
                    'updated_by' => $user->id,
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }
        $response = config('response.common.success');
        $response['data'] = Company::with(['owner'])->where(['id' => request('id')])->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:company,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            Company::where(['id' => request('id')])->delete();
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
        $query = Company::with(['owner'])
            ->when($request->filled(['owner_user_id']), function ($query) {
                return $query->where('owner_user_id', request('owner_user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('name_tc', 'like', '%' . $keyword . '%')
                        ->orWhere('name_en', 'like', '%' . $keyword . '%')
                        ->orWhere('address', 'like', '%' . $keyword . '%')
                        ->orWhere('secretary', 'like', '%' . $keyword . '%');
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
        $headers = [
            __('Owner'),
            __('Name'),
            __('No.'),
            __('Secretary'),
            __('Incorporation Date'),
            __('Address'),
            __('Registered Share Capital'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            $row = [
                $item['owner']['name'],
                $item['name_tc'],
                $item['number'],
                $item['secretary'],
                $item['incorporation_date'],
                $item['address'],
                $item['registered_share_capital'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('company', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }

}