<?php

namespace App\Http\Controllers\API\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent\Payment;
use App\Mylibs\Statistics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Log;

class PaymentLineChartController extends Controller
{
    //
    public function get(Request $request)
    {
        $period = strtolower(request('period'));
        // Log::debug($period);
        $result = null;
        $labels = null;
        $data = null;
        switch ($period) {
            case '30 days':
                $days = 30;
                $labels = Statistics::dailyLabels($days);
                $result = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('DAY(payment_date) as day'),
                    DB::raw('SUM(amount) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $data = Statistics::renderDailyData($result, $days);
                break;
            case '3 months':
                $days = 90;
                $labels = Statistics::dailyLabels($days);
                $result = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('DAY(payment_date) as day'),
                    DB::raw('SUM(amount) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $data = Statistics::renderDailyData($result, $days);
                break;
            case '12 months':
                // $months = 90;
                $labels = Statistics::monthlyLabels();
                $result = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('SUM(amount) AS count'))
                    ->groupBy(DB::raw('year, month'))
                    ->orderBy(DB::raw('year, month'))
                    ->get();
                $data = Statistics::renderMonthlyData($result);
                break;
            default:
                $days = 30;
                $labels = Statistics::dailyLabels($days);
                $result = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('DAY(payment_date) as day'),
                    DB::raw('SUM(amount) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $data = Statistics::renderDailyData($result, $days);
                break;
        }

        // Log::debug($result);
        $response = config('response.common.success');
        $response['data'] = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total',
                    'backgroundColor' => 'rgba(24, 125, 160, 0.2)',
                    'borderColor' => '#2eadd3',
                    'pointHoverBackgroundColor' => '#fff',
                    'borderWidth' => 2,
                    'data' => $data,
                ],

            ],
        ];
        return response()->json($response, 200);
    }
}