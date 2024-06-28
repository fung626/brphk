<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company\BankAccountBalance;
use App\Models\Expenses;
use App\Models\Rent\Payment as RentPayment;
use App\Models\Tax;
use App\Mylibs\Common;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function get(Request $request)
    {
        $rentPayment = RentPayment::whereDate('created_at', Carbon::today())->sum('amount');
        $bankBalance = BankAccountBalance::whereDate('created_at', Carbon::today())->sum('balance');
        $expenses = Expenses::whereDate('created_at', Carbon::today())->sum('amount');
        $tax = Tax::whereDate('created_at', Carbon::today())->sum('amount');

        $response = config('response.common.success');
        $response['data'] = [
            'rent_payment' => Common::formatPrice($rentPayment),
            'bank_balance' => Common::formatPrice($bankBalance),
            'expenses' => Common::formatPrice($expenses),
            'tax' => Common::formatPrice($tax),
        ];
        return response()->json($response, 200);
    }

}