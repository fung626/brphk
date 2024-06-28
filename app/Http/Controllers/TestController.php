<?php

namespace App\Http\Controllers;

use App\Http\Resources\Rent\ArrearsCollection;
use App\Models\Rent;
use App\Mylibs\CashFlow;
use App\Mylibs\Rent as RentLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// use Spatie\Permission\Models\Permission;

class TestController extends Controller
{
    //
    public function __construct()
    {
        $s = 559315.4299999999;
        dd(CashFlow::total());
    }

    public function post(Request $request)
    {
        dd(Auth::user());
    }

    public function get(Request $request)
    {
        $rents = Rent::hasRentArrears()->paginate(10, ['*'], 'page', 1);
        $result = RentLib::withRentArrearsMonthProperties($rents);
        $d = new ArrearsCollection($result['data']);
        dd($d[0]->actions);
        $data = $result['data'];
        $rows = [];
        foreach ($result['data'] as $item) {
            $row = [
                $item->user->name,
                $item->owner,
                $item->tenant,
                $item->property,
                Common::formatPrice($item->amount),
                $item->start_date,
                $item->fix_term_tenancy_date,
                $item->break_clause_date,
            ];
            foreach ($result['months'] as $month) {
                $row[] = isset($item->$month) ? $item->$month : false;
            }
            // dd($item, $row);
            $rows[] = $row;
        }
        dd($result['headers'], $rows);
    }
}