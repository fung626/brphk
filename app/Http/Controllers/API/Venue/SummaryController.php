<?php

namespace App\Http\Controllers\API\Venue;

use App\Http\Controllers\Controller;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use App\Mylibs\Venue as VenueLib;
use Illuminate\Http\Request;
use Validator;

class SummaryController extends Controller
{
    //

    public function get(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'venue_id' => 'required|string|exists:venue,id',
            'from_month' => 'required',
            'to_month' => 'required',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $from = request('from_month') . '-01';
        $to = request('to_month') . '-29';
        $monthRange = Common::getMonthsFromRange($from, $to);

        $limit = config('constant.summary.limit.months');
        if (count($monthRange) > $limit) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "The range should not exceed " . $limit . " months";
            return response()->json($response, 400);
        }

        $data = VenueLib::summary([request('venue_id')], $monthRange);

        $response = config('response.common.success');
        $response['data'] = $data;

        return response()->json($response, 200);
    }

    public function export(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'venue_id' => 'required|string|exists:venue,id',
            'from_month' => 'required',
            'to_month' => 'required',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $from = request('from_month') . '-01';
        $to = request('to_month') . '-29';
        $monthRange = Common::getMonthsFromRange($from, $to);

        $result = VenueLib::summary([request('venue_id')], $monthRange);

        $headers = [];
        $rows = [];

        foreach ($result['data'][0] as $key => $value) {
            $headers[] = $value;
        }

        for ($i = 1; $i < count($result['data']); $i++) {
            $temp = [];
            ksort($result['data'][$i]);
            foreach ($result['data'][$i] as $key => $value) {
                $temp[] = $value;
            }
            $rows[] = $temp;
        }

        $path = MyPhpOffice::exportTableWithPath(
            'venue-summary',
            $rows,
            $headers,
            'pdf'
        );

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }

}