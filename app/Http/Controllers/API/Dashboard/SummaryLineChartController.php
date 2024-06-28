<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company\BankAccountBalance;
use App\Models\Rent\Payment;
use App\Mylibs\Statistics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryLineChartController extends Controller
{
    //

    public function get(Request $request)
    {
        $period = strtolower(request('period'));
        // Log::debug($period);
        $result = null;
        $labels = null;
        $payment = null;
        $balance = null;

        switch ($period) {
            case '30 days':
                $days = 30;
                $labels = Statistics::dailyLabels($days);
                $_payment = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('DAY(payment_date) as day'),
                    DB::raw('SUM(amount) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $_balance = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(balance) AS count'))
                    ->where('created_at', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $payment = Statistics::renderDailyData($_payment, $days);
                $balance = Statistics::renderDailyData($_balance, $days);
                break;
            case '3 months':
                $days = 90;
                $labels = Statistics::dailyLabels($days);
                $_payment = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('DAY(payment_date) as day'),
                    DB::raw('SUM(amount) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $_balance = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(balance) AS count'))
                    ->where('created_at', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $payment = Statistics::renderDailyData($_payment, $days);
                $balance = Statistics::renderDailyData($_balance, $days);
                break;
            case '12 months':
                // $months = 90;
                $labels = Statistics::monthlyLabels();
                $_payment = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('SUM(amount) AS count'))
                    ->groupBy(DB::raw('year, month'))
                    ->orderBy(DB::raw('year, month'))
                    ->get();
                $_balance = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(balance) AS count'))
                    ->groupBy(DB::raw('year, month'))
                    ->orderBy(DB::raw('year, month'))
                    ->get();
                $payment = Statistics::renderMonthlyData($_payment);
                $balance = Statistics::renderMonthlyData($_balance);
                break;
            default:
                $days = 30;
                $labels = Statistics::dailyLabels($days);
                $_payment = Payment::select(
                    DB::raw('YEAR(payment_date) as year'),
                    DB::raw('MONTH(payment_date) as month'),
                    DB::raw('DAY(payment_date) as day'),
                    DB::raw('SUM(amount) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $_balance = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(balance) AS count'))
                    ->where('payment_date', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $payment = Statistics::renderDailyData($_payment, $days);
                $balance = Statistics::renderDailyData($_balance, $days);
                break;
        }

        // Log::debug($result);
        $response = config('response.common.success');
        $response['data'] = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Rent Payment',
                    'backgroundColor' => 'rgba(24, 125, 160, 0.2)',
                    'borderColor' => '#2eadd3',
                    'pointHoverBackgroundColor' => '#fff',
                    'borderWidth' => 2,
                    'data' => $payment,
                ],
                [
                    'label' => 'Bank Balance',
                    'backgroundColor' => 'rgba(50, 168, 82, 0.2)',
                    'borderColor' => '#1d6330',
                    'pointHoverBackgroundColor' => '#fff',
                    'borderWidth' => 2,
                    'data' => $balance,
                ],

            ],
        ];
        return response()->json($response, 200);
    }
}