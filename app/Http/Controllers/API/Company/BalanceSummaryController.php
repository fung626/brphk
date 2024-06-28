<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\BalanceSummaryCollection;
use App\Models\Company\BankAccountBalance;
use App\Mylibs\Common;
use Illuminate\Http\Request;

class BalanceSummaryController extends Controller
{
    //
    public function get(Request $request)
    {

        $query = BankAccountBalance::with(['user', 'company', 'bank'])
            ->when($request->filled(['company_id']), function ($query) {
                return $query->where('company_id', request('company_id'));
            })
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['from_date', 'to_date']), function ($query) {
                $from = date(request('from_date'));
                $to = date(request('to_date'));
                return $query->whereBetween('updated_at', [$from, $to]);
            });

        $sum = $query->sum('balance');
        $result = $query->get();
        $resource = new BalanceSummaryCollection($result);
        $response = config('response.common.success');
        $response['data'] = [
            'data' => $resource,
            'total' => $result->count(),
            'sum' => Common::formatPrice($sum),
        ];

        return response()->json($response, 200);
    }

}