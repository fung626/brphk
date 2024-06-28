<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpectedExpensesCollection;
use App\Models\ExpectedExpenses;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class ExpectedExpensesController extends Controller
{
    //
    public function get(Request $request)
    {

        $user = Auth::user();

        $query = ExpectedExpenses::with(['users'])
            ->select('expected_expenses.*')
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('user_id', 'like', '%' . $keyword . '%')
                        ->orWhere('company', 'like', '%' . $keyword . '%')
                        ->orWhere('item', 'like', '%' . $keyword . '%');
                });
            })
            ->leftJoin('users', 'users.id', '=', 'expected_expenses.user_id')
            ->groupBy('expected_expenses.id');

        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy !== "action") {
                    $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
                }
                $index++;
            }
        }

        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
        } else {
            $result = $query->get();
        }

        $response = config('response.common.success');
        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
            $resource = new ExpectedExpensesCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new ExpectedExpensesCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:expected_expenses,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $result = ExpectedExpenses::with(['users'])
            ->where(['id' => request('id')])
            ->first();

        $response = config('response.common.success');
        $response['data'] = $result;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            ExpectedExpenses::create([
                'user_id' => $user->id,
                'company' => request('company'),
                'item' => request('item'),
                'amount' => request('amount'),
                'date' => request('date'),
                'remark' => request('remark'),
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
            'id' => 'required|string|exists:expected_expenses,id',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            ExpectedExpenses::where(['id' => request('id')])
                ->update([
                    'company' => request('company'),
                    'item' => request('item'),
                    'amount' => request('amount'),
                    'date' => request('date'),
                    'remark' => request('remark'),
                    'updated_by' => $user->id,
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        $response['data'] = ExpectedExpenses::with(['users'])
            ->where(['id' => request('id')])
            ->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:expected_expenses,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            ExpectedExpenses::where(['id' => request('id')])->delete();
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
        $query = ExpectedExpenses::with(['users'])
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('user_id', 'like', '%' . $keyword . '%')
                        ->orWhere('company', 'like', '%' . $keyword . '%')
                        ->orWhere('item', 'like', '%' . $keyword . '%');
                });
            });

        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy !== "action") {
                    $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
                }
                $index++;
            }
        }

        $result = $query->get();
        $headers = [
            __('User'),
            __('Item'),
            __('Amount'),
            __('Remark'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            $row = [
                $item['users']['name'],
                $item['item'],
                Common::formatPrice($item['amount']),
                $item['remark'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('expected-expenses', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }
}
