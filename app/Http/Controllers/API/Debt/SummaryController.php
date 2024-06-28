<?php

namespace App\Http\Controllers\API\Debt;

use App\Http\Controllers\Controller;
use App\Mylibs\Common;
use Illuminate\Http\Request;
use Validator;

class SummaryController extends Controller
{
    //
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $dateRange = Common::getDatesFromRange(request('from_date'), request('to_date'));

        $limit = config('constant.summary.limit.days');
        if (count($dateRange) > $limit) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "The range should not exceed " . $limit . " days";
            return response()->json($response, 400);
        }

    }

}