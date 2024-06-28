<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\BankAccountBalanceCollection;
use App\Models\Company\BankAccount;
use App\Models\Company\BankAccountBalance;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class BankAccountBalanceController extends Controller
{
    //

    public function get(Request $request)
    {
        $query = BankAccountBalance::with(['user', 'company', 'bank'])
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['company_id']), function ($query) {
                return $query->where('company_id', request('company_id'));
            })
            ->when($request->filled(['company_bank_account_id']), function ($query) {
                return $query->where('company_bank_account_id', request('company_bank_account_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%');
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
            $resource = new BankAccountBalanceCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new BankAccountBalanceCollection($result);
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

        $result = BankAccountBalance::with(['user', 'company', 'bankAccount'])
            ->where(['id' => request('id')])
            ->first();
        $response = config('response.common.success');
        $response['data'] = $result;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_bank_account_id' => 'required|string|exists:company_bank_account,id',
            'balance' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        $bankAccountExists = BankAccount::where(['owner_user_id' => $user->id])->first();
        if ($user->role !== "admin" && !$bankAccountExists) {
            $response = config('response.common.fail.unauthorised');
            return response()->json($response, 405);
        }

        $bankAccount = BankAccount::where(['id' => request('company_bank_account_id')])->first();
        $companyId = $bankAccount->company_id;
        $companyBankAccountId = $bankAccount->id;

        try {

            $bankAccountBalanceExists = BankAccountBalance::where(['company_bank_account_id' => $companyBankAccountId])
                ->whereDate('created_at', Carbon::today())
                ->first();

            if ($bankAccountBalanceExists) {
                BankAccountBalance::where(['id' => $bankAccountBalanceExists->id])
                    ->update([
                        'balance' => request('balance'),
                        'remark' => request('remark'),
                    ]);
            } else {
                BankAccountBalance::create([
                    'user_id' => $user->id,
                    'company_id' => $companyId,
                    'company_bank_account_id' => $companyBankAccountId,
                    'balance' => request('balance'),
                    'remark' => request('remark'),
                    'updated_by' => $user->id,
                ]);
            }

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
            'id' => 'required|string|exists:company_bank_account_balance,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        $exists = BankAccountBalance::where(['id' => request('id'), 'user_id' => $user->id])->first();
        if ($user->role !== "admin" && !$exists) {
            $response = config('response.common.fail.unauthorised');
            return response()->json($response, 405);
        }

        try {
            BankAccountBalance::where(['id' => request('id')])
                ->update([
                    'balance' => request('balance'),
                    'remark' => request('remark'),
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
        $response['data'] = BankAccountBalance::where(['id' => request('id')])->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:company_bank_account_balance,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        $exists = BankAccountBalance::where(['id' => request('id'), 'user_id' => $user->id])->first();
        if ($user->role !== "admin" && !$exists) {
            $response = config('response.common.fail.unauthorised');
            return response()->json($response, 405);
        }

        try {
            BankAccountBalance::where(['id' => request('id')])->delete();
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
        $query = BankAccountBalance::with(['user', 'company', 'bank'])
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['company_id']), function ($query) {
                return $query->where('company_id', request('company_id'));
            })
            ->when($request->filled(['company_bank_account_id']), function ($query) {
                return $query->where('company_bank_account_id', request('company_bank_account_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%');
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
            __('User'),
            __('Company'),
            __('Bank'),
            __('Bank Account Type'),
            __('Balance'),
            __('Remark'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            $row = [
                $item['user']['name'],
                $item['company']['name_tc'],
                $item['bank']['account_type'],
                $item['balance'],
                $item['remark'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('company-bank-account', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }

}