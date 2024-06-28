<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Mylibs\CashFlow;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
// use Illuminate\Support\Facades\Log;
use Validator;

class CashFlowController extends Controller
{
    //
    public function get(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|min:1',
            'users.*.id' => 'required|exists:users,id',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $userIds = Arr::pluck(request('users'), 'id');

        if (count($userIds) > 6) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "You only allowed select up to 6 users";
            return response()->json($response, 400);
        }

        $users = Users::whereIn('id', $userIds)->get();
        // $users = request('users');
        $from = date(request('from_date'));
        $to = date(request('to_date'));
        $data = null;

        switch (request('type')) {
            case 'daily':
                $limit = config('constant.summary.limit.days');
                $range = Common::getDatesFromRange(request('from_date'), request('to_date'));
                if (count($range) > $limit) {
                    $response = config('response.common.fail.parameter');
                    $response['msg'] = "The range should not exceed " . $limit . " days";
                    return response()->json($response, 400);
                }
                $data = CashFlow::daily($userIds, $range, $from, $to);
                break;
            case 'monthly':
                $limit = config('constant.summary.limit.months');
                $range = Common::getMonthsFromRange(request('from_date'), request('to_date'));
                if (count($range) > $limit) {
                    $response = config('response.common.fail.parameter');
                    $response['msg'] = "The range should not exceed " . $limit . " months";
                    return response()->json($response, 400);
                }
                $data = CashFlow::monthly($userIds, $range, $from, $to);
                break;
        }

        $response = config('response.common.success');
        $response['data'] = $data;
        return response()->json($response, 200);

    }

    public function export(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'users' => 'required|array|min:1',
            'users.*.id' => 'required|exists:users,id',
            'from_date' => 'required|date_format:Y-m-d',
            'to_date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $userIds = Arr::pluck(request('users'), 'id');
        $dateRange = Common::getDatesFromRange(request('from_date'), request('to_date'));

        $limit = config('constant.summary.limit.users');
        if (count($userIds) > $limit) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "You only allowed select up to " . $limit . " users";
            return response()->json($response, 400);
        }

        $users = Users::whereIn('id', $userIds)->get();

        $from = date(request('from_date'));
        $to = date(request('to_date'));
        $result = null;

        switch (request('type')) {
            case 'daily':
                $limit = config('constant.summary.limit.days');
                $range = Common::getDatesFromRange(request('from_date'), request('to_date'));
                if (count($range) > $limit) {
                    $response = config('response.common.fail.parameter');
                    $response['msg'] = "The range should not exceed " . $limit . " days";
                    return response()->json($response, 400);
                }
                $result = CashFlow::daily($userIds, $range, $from, $to);
                break;
            case 'monthly':
                $limit = config('constant.summary.limit.months');
                $range = Common::getMonthsFromRange(request('from_date'), request('to_date'));
                if (count($range) > $limit) {
                    $response = config('response.common.fail.parameter');
                    $response['msg'] = "The range should not exceed " . $limit . " months";
                    return response()->json($response, 400);
                }
                $result = CashFlow::monthly($userIds, $range, $from, $to);
                break;
        }

        if (count($result['data']) === 0) {
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        $headers = [];
        $rows = [];
        foreach ($result['data'][0] as $key => $value) {
            $headers[] = $value;
        }

        for ($i = 1; $i < count($result['data']); $i++) {
            $temp = [];
            foreach ($result['data'][$i] as $value) {
                $temp[] = $value;
            }
            $rows[] = $temp;
        }

        // Log::debug($headers);
        $path = MyPhpOffice::exportTableWithPath(
            'cashflow',
            $rows,
            $headers,
            'pdf'
        );

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }

}