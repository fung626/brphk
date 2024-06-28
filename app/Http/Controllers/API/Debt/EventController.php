<?php

namespace App\Http\Controllers\API\Debt;

use App\Http\Controllers\Controller;
use App\Http\Resources\DebtEventCollection;
use App\Models\Debt;
use Illuminate\Http\Request;

class EventController extends Controller
{
    //

    public function get(Request $request)
    {
        $result = Debt::with(['users', 'schedule'])
            ->has('schedule')
            ->when($request->filled(['from_date', 'to_date']), function ($query) {
                $from = date(request('from_date'));
                $to = date(request('to_date'));
                return $query->whereBetween('due_date', [$from, $to]);;
            })->get();

        $resource = new DebtEventCollection($result);
        $response = config('response.common.success');
        $response['data'] = $resource;
        return response()->json($response, 200);
    }
}