<?php

namespace App\Http\Controllers\API\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\BankAccountBalance;
use App\Mylibs\Statistics;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashFlowLineChartController extends Controller
{
    //
    public function get(Request $request)
    {
        $period = strtolower(request('period'));
        $result = null;
        $labels = null;
        $data = null;

        switch ($period) {
            case '30 days':
                $days = 30;
                $labels = Statistics::dailyLabels($days);
                $result = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(balance) AS count'))
                    ->where('created_at', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $data = Statistics::renderDailyData($result, $days);
                break;
            case '3 months':
                $days = 90;
                $labels = Statistics::dailyLabels($days);
                $result = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(balance) AS count'))
                    ->where('created_at', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $data = Statistics::renderDailyData($result, $days);
                break;
            case '12 months':
                // $months = 90;
                $labels = Statistics::monthlyLabels();
                $result = BankAccountBalance::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('SUM(balance) AS count'))
                    ->groupBy(DB::raw('year, month'))
                    ->orderBy(DB::raw('year, month'))
                    ->get();
                $data = Statistics::renderMonthlyData($result);
                break;
            default:
                $days = 30;
                $labels = Statistics::dailyLabels($days);
                $result = Payment::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('DAY(created_at) as day'),
                    DB::raw('SUM(balance) AS count'))
                    ->where('created_at', '>=', Carbon::now()->subDay($days)->toDateTimeString())
                    ->groupBy(DB::raw('year, month, day'))
                    ->orderBy(DB::raw('year, month, day'))
                    ->get();
                $data = Statistics::renderDailyData($result, $days);
                break;
        }

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