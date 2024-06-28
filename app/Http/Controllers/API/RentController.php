<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentCollection;
use App\Http\Resources\RentResource;
use App\Models\Rent;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class RentController extends Controller
{
    //
    public function get(Request $request)
    {
        $user = Auth::user();
        $query = Rent::with(['users', 'payments'])
            ->select('rent.*')
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('rent.user_id', request('user_id'));
            })
            ->when($user->role === "employee", function ($query) use ($user) {
                return $query->where('rent.user_id', $user->id);
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('owner', 'like', '%' . $keyword . '%')
                        ->orWhere('tenant', 'like', '%' . $keyword . '%')
                        ->orWhere('property', 'like', '%' . $keyword . '%')
                        ->orWhere('remark', 'like', '%' . $keyword . '%');
                });
            })
            ->leftJoin('users', 'users.id', '=', 'rent.user_id')
            ->leftJoin('rent_payment', 'rent_payment.rent_id', '=', 'rent.id')
            ->groupBy('rent.id');

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

        $response = config('response.common.success');
        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
            $resource = new RentCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new RentCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function details(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:rent,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }
        $user = Auth::user();
        $result = Rent::with(['payments'])
            ->where(['id' => request('id')])
            ->when($user->role === "employee", function ($query) use ($user) {
                return $query->where('rent.user_id', $user->id);
            })
            ->first();
        $resource = new RentResource($result);

        $response = config('response.common.success');
        $response['data'] = $resource;
        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner' => 'required|string',
            'tenant' => 'required|string',
            'property' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'management_fee' => 'nullable|numeric|min:1',
            'rates' => 'nullable|numeric|min:1',
            'rent_per_square_foot' => 'nullable|numeric|min:1',
            'government_rent' => 'nullable|numeric|min:1',
            'other_fee' => 'nullable|numeric|min:1',
            'area' => 'nullable|numeric|min:1',
            'start_date' => 'nullable|date_format:Y-m-d',
            'fix_term_tenancy_date' => 'nullable|date_format:Y-m-d|after:start_date',
            'break_clause_date' => 'nullable|date_format:Y-m-d|after:start_date',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            Rent::create([
                'user_id' => $user->id,
                'owner' => request('owner'),
                'tenant' => request('tenant'),
                'property' => request('property'),
                'amount' => request('amount'),
                'management_fee' => request('management_fee'),
                'rates' => request('rates'),
                'rent_per_square_foot' => request('rent_per_square_foot'),
                'government_rent' => request('government_rent'),
                'other_fee' => request('other_fee'),
                'area' => request('area'),
                'start_date' => request('start_date'),
                'fix_term_tenancy_date' => request('fix_term_tenancy_date'),
                'break_clause_date' => request('break_clause_date'),
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
            'id' => 'required|string|exists:rent,id',
            'owner' => 'required|string',
            'tenant' => 'required|string',
            'property' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'management_fee' => 'nullable|numeric|min:1',
            'rates' => 'nullable|numeric|min:1',
            'rent_per_square_foot' => 'nullable|numeric|min:1',
            'government_rent' => 'nullable|numeric|min:1',
            'other_fee' => 'nullable|numeric|min:1',
            'area' => 'nullable|numeric|min:1',
            'start_date' => 'nullable|date_format:Y-m-d',
            'fix_term_tenancy_date' => 'nullable|date_format:Y-m-d|after:start_date',
            'break_clause_date' => 'nullable|date_format:Y-m-d|after:start_date',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            Rent::where(['id' => request('id')])
                ->update([
                    'owner' => request('owner'),
                    'tenant' => request('tenant'),
                    'property' => request('property'),
                    'amount' => request('amount'),
                    'management_fee' => request('management_fee'),
                    'rates' => request('rates'),
                    'rent_per_square_foot' => request('rent_per_square_foot'),
                    'government_rent' => request('government_rent'),
                    'other_fee' => request('other_fee'),
                    'area' => request('area'),
                    'start_date' => request('start_date'),
                    'fix_term_tenancy_date' => request('fix_term_tenancy_date'),
                    'break_clause_date' => request('break_clause_date'),
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
        $response['data'] = Rent::with(['payments'])
            ->where(['id' => request('id')])
            ->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:rent,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            Rent::where(['id' => request('id')])->delete();
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
        $query = Rent::with(['user', 'payments'])
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('owner', 'like', '%' . $keyword . '%')
                        ->orWhere('tenant', 'like', '%' . $keyword . '%')
                        ->orWhere('property', 'like', '%' . $keyword . '%')
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
            __('rent.property.owner'),
            __('rent.tenant'),
            __('rent.property.name'),
            __('rent.area'),
            __('rent.amount'),
            __('rent.rentpersquarefoot'),
            __('rent.governmentrent'),
            __('rent.rates'),
            __('rent.other_fee'),
            __('rent.startdate'),
            __('rent.fttdate'),
            __('rent.bcate'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            // [$keys, $values] = Arr::divide($item);
            // Log::debug($item);
            $row = [
                $item['user']['name'],
                $item['owner'],
                $item['tenant'],
                $item['property'],
                $item['area'],
                Common::formatPrice($item['amount']),
                Common::formatPrice($item['rent_per_square_foot']),
                Common::formatPrice($item['government_rent']),
                Common::formatPrice($item['rates']),
                Common::formatPrice($item['other_fee']),
                $item['start_date'],
                $item['fix_term_tenancy_date'],
                $item['break_clause_date'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('rent', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

}