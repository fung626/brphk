<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExpensesCollection;
use App\Models\Expenses;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class ExpensesController extends Controller
{
    //
    public function get(Request $request)
    {

        $user = Auth::user();

        $query = Expenses::with(['user'])
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($user->role === "employee", function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('user_id', 'like', '%' . $keyword . '%')
                        ->orWhere('cheque_bank', 'like', '%' . $keyword . '%')
                        ->orWhere('cheque_no', 'like', '%' . $keyword . '%')
                        ->orWhere('cheque_issuer', 'like', '%' . $keyword . '%')
                        ->orWhere('remark', 'like', '%' . $keyword . '%');
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

        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
        } else {
            $result = $query->get();
        }

        $response = config('response.common.success');
        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
            $resource = new ExpensesCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new ExpensesCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:expenses,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $result = Expenses::with(['user'])
            ->where(['id' => request('id')])
            ->first();

        $response = config('response.common.success');
        $response['data'] = $result;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cheque_bank' => 'required|string',
            'cheque_no' => 'required|string',
            'cheque_issuer' => 'required|string',
            'signer' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'issued_company' => 'required|string',
            'pay_to' => 'required|string',
            'cheque_issued_date' => 'required|date_format:Y-m-d',
            'internal_transfer' => 'required',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            Expenses::create([
                'user_id' => $user->id,
                'cheque_bank' => request('cheque_bank'),
                'cheque_no' => request('cheque_no'),
                'cheque_issuer' => request('cheque_issuer'),
                'signer' => request('signer'),
                'amount' => request('amount'),
                'issued_company' => request('issued_company'),
                'pay_to' => request('pay_to'),
                'cheque_issued_date' => request('cheque_issued_date'),
                'internal_transfer' => request('internal_transfer'),
                'item' => request('item'),
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
            'id' => 'required|string|exists:expenses,id',
            'cheque_bank' => 'required|string',
            'cheque_no' => 'required|string',
            'cheque_issuer' => 'required|string',
            'signer' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'issued_company' => 'required|string',
            'pay_to' => 'required|string',
            'cheque_issued_date' => 'required|date_format:Y-m-d',
            'internal_transfer' => 'required',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {

            Expenses::where(['id' => request('id')])
                ->update([
                    'cheque_bank' => request('cheque_bank'),
                    'cheque_no' => request('cheque_no'),
                    'cheque_issuer' => request('cheque_issuer'),
                    'signer' => request('signer'),
                    'amount' => request('amount'),
                    'issued_company' => request('issued_company'),
                    'pay_to' => request('pay_to'),
                    'cheque_issued_date' => request('cheque_issued_date'),
                    'item' => request('item'),
                    'internal_transfer' => request('internal_transfer'),
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
        $response['data'] = Expenses::with(['user'])
            ->where(['id' => request('id')])
            ->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:expenses,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            Expenses::where(['id' => request('id')])->delete();
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
        $query = Expenses::with(['user'])
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('user_id', 'like', '%' . $keyword . '%')
                        ->orWhere('cheque_bank', 'like', '%' . $keyword . '%')
                        ->orWhere('cheque_no', 'like', '%' . $keyword . '%')
                        ->orWhere('cheque_issuer', 'like', '%' . $keyword . '%')
                        ->orWhere('remark', 'like', '%' . $keyword . '%');
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
            __('Bank'),
            __('No.'),
            __('Cheque Issuer'),
            __('Signer'),
            __('Amount'),
            __('Issued Company'),
            __('Pay to'),
            __('Cheque Issued Date'),
            __('Internal Transfer'),
            __('Item'),
            __('Remark'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            $row = [
                $item['user']['name'],
                $item['cheque_bank'],
                $item['cheque_no'],
                $item['cheque_issuer'],
                $item['signer'],
                Common::formatPrice($item['amount']),
                $item['issued_company'],
                $item['pay_to'],
                $item['cheque_issued_date'],
                $item['internal_transfer'] === 1 ? __('Yes') : __('No'),
                $item['item'],
                $item['remark'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('expenses', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

}
