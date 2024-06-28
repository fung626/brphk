<?php

namespace App\Http\Controllers\API\Profit;

use App\Http\Controllers\Controller;
use App\Models\Profit;
use App\Mylibs\MyPhpOffice;
use App\Mylibs\Profit as ProfitLib;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SummaryController extends Controller
{
    //
    public function get(Request $request)
    {

        $companyIds = Arr::pluck(request('companies'), 'id');
        $data = ProfitLib::summary($companyIds, request('years'));

        $response = config('response.common.success');
        $response['data'] = $data;

        return response()->json($response, 200);
    }

    public function export(Request $request)
    {
        $companyIds = Arr::pluck(request('companies'), 'id');
        $result = ProfitLib::summary($companyIds, request('years'));

        $headers = [];
        $rows = [];

        foreach ($result['data'][0] as $key => $value) {
            $headers[] = $value;
        }

        for ($i = 1; $i < count($result['data']); $i++) {
            $temp = [];
            Log::debug($result['data'][$i]);
            foreach ($result['data'][$i] as $value) {
                $temp[] = $value;
            }
            $rows[] = $temp;
        }

        $path = MyPhpOffice::exportTableWithPath(
            'profit-summary',
            $rows,
            $headers,
            'pdf'
        );

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);

    }

}