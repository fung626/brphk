<?php

namespace App\Http\Controllers\API\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent;
use App\Models\Rent\Payment;
use App\Models\Users;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;
use Validator;

class PaymentSummaryController extends Controller
{
    //

    public function get(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'rents' => 'required|array|min:1',
            'rents.*.id' => 'required|exists:rent,id',
            'from_month' => 'required|date_format:Y-m',
            'to_month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $from = request('from_month') . '-01';
        $to = request('to_month') . '-29';
        $rentIds = Arr::pluck(request('rents'), 'id');
        $monthRange = Common::getMonthsFromRange($from, $to);
        // Log::debug($monthRange);

        if (count($rentIds) > 6) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "You only allowed select up to 6 users";
            return response()->json($response, 400);
        }

        $limit = config('constant.summary.limit.months');
        if (count($monthRange) > $limit) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "The range should not exceed " . $limit . " months";
            return response()->json($response, 400);
        }

        $select = ['user_id', 'rent_id'];
        $headers = [
            ['text' => __("User"), 'value' => "users.name"],
            ['text' => __("rent.property.owner"), 'value' => "rent.owner"],
            ['text' => __("rent.tenant"), 'value' => "rent.tenant"],
            ['text' => __("rent.amount"), 'value' => "rent.amount"],
            ['text' => __("rent.startdate"), 'value' => "rent.start_date"],
            ['text' => __("rent.fttdate"), 'value' => "rent.fix_term_tenancy_date"],
            ['text' => __("rent.bcdate"), 'value' => 'rent.break_clause_date'],
        ];
        // $index = count($select);

        $defaultDateData = [];

        $select = ['rent_payment.*', 'rent.owner'];
        foreach ($monthRange as $date) {
            $month = $date;
            // $_date = $date . '-01';
            $select[] = DB::raw('MAX(CASE WHEN (payment_month = "' . $month . '") THEN payment_date ELSE "－" END) AS "' . $month . '"');
            $headers[] = ['text' => $month, 'value' => $month, 'sortable' => false];
            $defaultDateData[$month] = '－';
            // $index++;
        }

        $sortBys = request('sort_by');
        $sortDescs = request('sort_desc');

        $query = Payment::with(['users', 'rent'])
            ->select($select)
            ->when($rentIds, function ($query) use ($rentIds) {
                return $query->whereIn('rent_id', $rentIds);
            })
            ->when($request->filled(['rent_id']), function ($query) {
                return $query->where('rent_id', request('rent_id'));
            })
            ->leftJoin('users', 'users.id', '=', 'rent_payment.user_id')
            ->leftJoin('rent', 'rent.id', '=', 'rent_payment.rent_id')
            ->groupBy('rent_id');

        // if ($request->filled(['sort_by', 'sort_desc'])) {
        //     $sortBys = request('sort_by');
        //     $sortDescs = request('sort_desc');
        //     $index = 0;
        //     foreach ($sortBys as $sortBy) {
        //         $sortDesc = $sortDescs[$index];
        //         if ($sortBy !== "action") {
        //             $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
        //         }
        //         $index++;
        //     }
        // }

        // $headers = [];
        $data = [];
        $payments = $query->get();

        foreach ($rentIds as $rentId) {
            $found = false;
            $id = '';
            foreach ($payments as $payment) {
                if ($rentId === $payment->rent_id) {
                    $found = true;
                    $_paymemt = $payment->toArray();
                    $_paymemt['rent']['amount'] = Common::formatPrice($_paymemt['rent']['amount']);
                    // foreach ($monthRange as $month) {
                    //     $_paymemt[$month] = Common::formatPrice($_paymemt[$month]);
                    // }
                    $data[] = $_paymemt;
                }
            }
            if (!$found) {
                $_data = $defaultDateData;
                $rent = Rent::where(['id' => $rentId])->first();
                $users = Users::where(['id' => $rent->user_id])->first();
                $rent->amount = Common::formatPrice($rent->amount);
                $_data['rent_id'] = $rentId;
                $_data["user_id"] = $rent->user_id;
                $_data["rent"] = $rent;
                $_data["users"] = $users;
                $data[] = $_data;
            }
        }

        $sorted = null;
        $collection = collect($data);
        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy !== "action") {
                    if ($sortDesc) {
                        $sorted = $collection->sortByDesc($sortBy);
                    } else {
                        $sorted = $collection->sortBy($sortBy);
                    }
                    // $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');

                }
                $index++;
            }

        };
        if ($sorted) {
            $data = $sorted->values()->all();
            // Log::debug($sorted);
        }

        $response = config('response.common.success');
        $response['data'] = [
            'headers' => $headers,
            'data' => $data,
            'total' => count($data),
            // 'sum' => Common::formatPrice($sum),
        ];

        return response()->json($response, 200);
    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rents' => 'required|array|min:1',
            'rents.*.id' => 'required|exists:rent,id',
            'from_month' => 'required|date_format:Y-m',
            'to_month' => 'required|date_format:Y-m',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $from = request('from_month') . '-01';
        $to = request('to_month') . '-29';
        $rentIds = Arr::pluck(request('rents'), 'id');
        $monthRange = Common::getMonthsFromRange($from, $to);

        if (count($rentIds) > 6) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "You only allowed select up to 6 users";
            return response()->json($response, 400);
        }

        $limit = config('constant.summary.limit.months');
        if (count($monthRange) > $limit) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "The range should not exceed " . $limit . " months";
            return response()->json($response, 400);
        }

        $select = ['user_id', 'rent_id'];
        $headers = [
            __("User"),
            __("rent.property.owner"),
            __("rent.tenant"),
            __("rent.amount"),
            __("rent.startdate"),
            __("rent.fttdate"),
            __("rent.bcdate"),
        ];

        $defaultDateData = [];
        foreach ($monthRange as $month) {
            // $month = $date;
            // $_date = $date . '-01';
            $select[] = DB::raw('MAX(CASE WHEN (payment_month = "' . $month . '") THEN payment_date ELSE "－" END) AS "' . $month . '"');
            $headers[] = $month;
            $defaultDateData[$month] = '－';
            // $index++;
        }

        $query = Payment::with(['users', 'rent'])
            ->select($select)
            ->when($rentIds, function ($query) use ($rentIds) {
                return $query->whereIn('rent_id', $rentIds);
            })
            ->when($request->filled(['rent_id']), function ($query) {
                return $query->where('rent_id', request('rent_id'));
            })
            ->groupBy('rent_id');

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

        // $headers = [];
        $data = [];
        $result = $query->get();

        foreach ($result->toArray() as $item) {
            // [$keys, $values] = Arr::divide($item);
            // $headers = $keys;
            $row = [
                $item['users']['name'],
                $item['rent']['owner'],
                $item['rent']['tenant'],
                $item['rent']['amount'],
                $item['rent']['start_date'],
                $item['rent']['fix_term_tenancy_date'],
                $item['rent']['break_clause_date'],
            ];
            foreach ($monthRange as $month) {
                $row[] = $item[$month];
            }
            $rows[] = $row;
        }
        $path = MyPhpOffice::exportTableWithPath(
            'rent-payment-summary',
            $rows,
            $headers,
            'pdf'
        );

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

}