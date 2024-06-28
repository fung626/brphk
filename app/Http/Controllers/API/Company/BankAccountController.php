<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\BankAccountCollection;
use App\Models\Company\BankAccount;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class BankAccountController extends Controller
{

    //
    public function get(Request $request)
    {

        $user = Auth::user();

        $query = BankAccount::with(['owner', 'company'])
            ->select('company_bank_account.*')
            ->when($request->filled(['company_id']), function ($query) {
                return $query->where('company_id', request('company_id'));
            })
            ->when($request->filled(['owner_user_id']), function ($query) {
                return $query->where('company_bank_account.owner_user_id', request('owner_user_id'));
            })
            ->when($user->role === "employee", function ($query) use ($user) {
                return $query->where('company_bank_account.owner_user_id', $user->id);
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('bank', 'like', '%' . $keyword . '%')
                        ->orWhere('account_type', 'like', '%' . $keyword . '%');
                });
            })
            ->leftJoin('users as owner', 'owner.id', '=', 'company_bank_account.owner_user_id')
            ->leftJoin('company', 'company.id', '=', 'company_bank_account.company_id')
            ->groupBy('company_bank_account.id');

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
            $resource = new BankAccountCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new BankAccountCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:company_bank_account,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $result = BankAccount::with(['owner', 'company'])->where(['id' => request('id')])->first();
        $response = config('response.common.success');
        $response['data'] = $result;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|string|exists:company,id',
            'company.id' => 'required_without:company_id|string|exists:company,id',
            'owner' => 'required',
            'bank' => 'required|string',
            'account_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            BankAccount::create([
                'user_id' => $user->id,
                'owner_user_id' => request('owner')['id'],
                'company_id' => $request->filled('company_id') ? request('company_id') : request('company.id'),
                'bank' => request('bank'),
                'account_type' => request('account_type'),
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
            'id' => 'required|string|exists:company_bank_account,id',
            'owner.id' => 'required|exists:users,id',
            'bank' => 'required|string',
            'account_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        $result = BankAccount::where(['id' => request('id'), 'user_id' => $user->id])->first();
        if (!$result) {
            $response = config('response.common.fail.unauthorised');
            return response()->json($response, 401);
        }

        try {
            BankAccount::where(['id' => request('id')])
                ->update([
                    'owner_user_id' => request('owner')['id'],
                    'bank' => request('bank'),
                    'account_type' => request('account_type'),
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
        $response['data'] = BankAccount::where(['id' => request('id')])->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:company_bank_account,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        if ($user->role !== "admin") {
            $response = config('response.common.fail.unauthorised');
            return response()->json($response, 405);
        }

        try {
            BankAccount::where(['id' => request('id')])->delete();
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
        $query = BankAccount::with(['owner', 'company'])
            ->when($request->filled(['company_id']), function ($query) {
                return $query->where('company_id', request('company_id'));
            })
            ->when($request->filled(['owner_user_id']), function ($query) {
                return $query->where('owner_user_id', request('owner_user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('bank', 'like', '%' . $keyword . '%')
                        ->orWhere('account_type', 'like', '%' . $keyword . '%');
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
            __('Bank'),
            __('Bank Account Type'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            $row = [
                $item['owner']['name'],
                $item['bank'],
                $item['account_type'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('company-bank-account-balance', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }
}