<?php

namespace App\Mylibs;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;

class Statistics
{

    public static function months($count = 12)
    {
        $year = date('Y');
        $table = '';
        for ($i = $count; $i >= 1; $i--) {
            // $months[] = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
            if ($i == $count) { // first item
                $table .= '(SELECT "' . date("Y-m-d", strtotime(date('Y-m-01') . " -$i months")) . '" AS merge_date';
            } else if ($i == 1) { // last item
                $table .= ' UNION SELECT "' . date("Y-m-d", strtotime(date('Y-m-01') . " -$i months")) . '" AS merge_date) AS months';
            } else {
                $table .= ' UNION SELECT "' . date("Y-m-d", strtotime(date('Y-m-01') . " -$i months")) . '" AS merge_date';
            }
        }

        return DB::table(DB::raw($table))
            ->select(
                DB::raw('MONTH(merge_date) AS month'),
                DB::raw('DATE_FORMAT(merge_date, "%b") AS abbreviation_month'),
                DB::raw('YEAR(merge_date) AS year')
            )->get();
    }

    public static function renderLineChartPeriod($unit = 'Year', $value = 1)
    {
        $interval = null;
        $start = null;
        switch ($unit) {
            case 'Day':
            case 'day':
                $start = Carbon::today()->subDays($value);
                switch ($value) {
                    case 1:
                        $interval = '1 hour';
                        break;
                    case 5:
                        $interval = '12 hours';
                        break;
                    default:
                        $interval = '12 hours';
                        break;
                }
                break;
            case 'Month':
            case 'month':
                $start = Carbon::today()->subMonths($value);
                switch ($value) {
                    case 3:
                        $interval = '3 days';
                        break;
                    case 6:
                        $interval = '7 days';
                        break;
                    default:
                        $interval = '7 days';
                        break;
                }
                break;
            case 'Year':
            case 'year':
                $start = Carbon::today()->subYears($value);
                switch ($value) {
                    case 1:
                        $interval = '1 month';
                        break;
                    default:
                        $interval = '1 month';
                        break;
                }
                break;
        }

        $carbonPeriod = new CarbonPeriod($start, $interval, Carbon::today());

        $dates = [];
        foreach ($carbonPeriod as $carbon) {
            switch ($unit) {
                case 'Day':
                case 'day':
                    $dates[] = [
                        'hour' => $carbon->format('H'),
                        'day' => $carbon->format('d'),
                        'month' => $carbon->format('m'),
                        'year' => $carbon->format('Y'),
                        'abbreviation_month' => $carbon->format('M'),
                    ];
                    break;
                case 'Month':
                case 'month':
                    $dates[] = [
                        'day' => $carbon->format('d'),
                        'month' => $carbon->format('m'),
                        'year' => $carbon->format('Y'),
                        'abbreviation_month' => $carbon->format('M'),
                    ];
                    break;
                case 'Year':
                case 'year':
                    $dates[] = [
                        'month' => $carbon->format('m'),
                        'year' => $carbon->format('Y'),
                        'abbreviation_month' => $carbon->format('M'),
                    ];
                    break;
            }
        }
        // dd($dates);
        return $dates;
    }

    public static function renderLineChartData($data, $dates)
    {
        $_data = [];
        $_labels = [];
        // dd($dates);
        foreach ($dates as &$date) {
            $exsits = false;
            foreach ($data as &$_value) {
                // dd($_value, $dates);
                if ((isset($date['year']) && isset($_value->year)) &&
                    (isset($date['month']) && isset($_value->month)) &&
                    (isset($date['day']) && isset($_value->day))) {
                    // dd($date['day'] * 1);
                    if ($date['year'] == $_value->year &&
                        $date['month'] * 1 == $_value->month &&
                        $date['day'] * 1 == $_value->day) {
                        $exsits = true;
                        $_data[] = $_value->count;
                        $_labels[] = $date['day'] . ' ' . $date['abbreviation_month'] . ' ' . $date['year'];
                    }
                } else if ((isset($date['year']) && isset($_value->year)) &&
                    (isset($date['month']) && isset($_value->month))) {
                    if ($date['year'] == $_value->year &&
                        $date['month'] * 1 == $_value->month) {
                        $exsits = true;
                        $_data[] = $_value->count;
                        $_labels[] = $date['abbreviation_month'] . ' ' . $date['year'];
                    }
                }
            }
            if (!$exsits) {
                // dd($_value, $dates);
                $_data[] = 0;
                if ((isset($date['year']) && isset($_value->year)) &&
                    (isset($date['month']) && isset($_value->month)) &&
                    (isset($date['day']) && isset($_value->day))) {
                    $_labels[] = $date['day'] . ' ' . $date['abbreviation_month'] . ' ' . $date['year'];
                } else if ((isset($date['year']) && isset($_value->year)) &&
                    (isset($date['month']) && isset($_value->month))) {
                    $_labels[] = $date['abbreviation_month'] . ' ' . $date['year'];
                }
            }
        }
        return [
            'labels' => $_labels,
            'data' => $_data,
        ];
    }

    public static function monthlyLabels($count = 12)
    {
        $months = self::months($count);
        foreach ($months as &$value) {
            $labels[] = $value->abbreviation_month . ' ' . $value->year;
        }
        return $labels;
    }

    public static function renderMonthlyData($data, $count = 12)
    {
        $months = self::months($count);
        $_data = [];
        foreach ($months as &$value) {
            $exsits = false;
            // $labels[] = $value->abbreviation_month . ' ' . $value->year;
            foreach ($data as &$_value) {
                if ($_value->year === $value->year && $_value->month === $value->month) {
                    $exsits = true;
                    $_data[] = $_value->count;
                }
            }
            if (!$exsits) {
                $_data[] = 0;
            }
        }
        return $_data;
    }

    public static function days($count = 30)
    {
        $year = date('Y');
        $table = '';
        for ($i = $count; $i >= 1; $i--) {
            // $months[] = date("Y-m-d", strtotime(date('Y-m-01') . " -$i months"));
            if ($i == $count) { // first item
                $table .= '(SELECT "' . date("Y-m-d", strtotime(date('Y-m-d') . " -$i days")) . '" AS merge_date';
            } else if ($i == 1) { // last item
                $table .= ' UNION SELECT "' . date("Y-m-d", strtotime(date('Y-m-d') . " -$i days")) . '" AS merge_date) AS day';
            } else {
                $table .= ' UNION SELECT "' . date("Y-m-d", strtotime(date('Y-m-d') . " -$i days")) . '" AS merge_date';
            }
        }
        return DB::table(DB::raw($table))
            ->select(
                DB::raw('DAY(merge_date) AS day'),
                DB::raw('MONTH(merge_date) AS month'),
                DB::raw('DATE_FORMAT(merge_date, "%b") AS abbreviation_month'),
                DB::raw('YEAR(merge_date) AS year')
            )->get();
    }

    public static function dailyLabels($count = 30)
    {
        $months = self::days($count);
        foreach ($months as &$value) {
            $labels[] = $value->day . ' ' . $value->abbreviation_month . ' ' . $value->year;
        }
        return $labels;
    }

    public static function renderDailyData($data, $count = 30)
    {
        $days = self::days($count);
        $_data = [];
        foreach ($days as &$value) {
            $exsits = false;
            // $labels[] = $value->abbreviation_month . ' ' . $value->year;
            foreach ($data as &$_value) {
                if ($_value->year === $value->year && $_value->month === $value->month && $_value->day === $value->day) {
                    $exsits = true;
                    $_data[] = $_value->count;
                }
            }
            if (!$exsits) {
                $_data[] = 0;
            }
        }
        return $_data;
    }

    public static function hourlyLabels()
    {
        $hours = self::hours();
        foreach ($hours as &$value) {
            $labels[] = $value->date . ' ' . sprintf("%02d", $value->hour) . ':00';
        }
        return $labels;
    }

    public static function hours()
    {
        $table = '';
        for ($i = 0; $i < 24; $i++) {
            if ($i == 0) { // first item
                $table .= 'SELECT CURDATE() as date, ' . $i . ' as hour';
            } else {
                $table .= ' UNION SELECT CURDATE() as date, ' . $i . ' as hour';
            }

        }
        return DB::select($table);
    }

    public static function renderHourlyData($data)
    {
        $hours = self::hours();
        $_data = [];
        foreach ($hours as &$value) {
            $exsits = false;
            // $labels[] = $value->abbreviation_month . ' ' . $value->year;
            foreach ($data as &$_value) {
                if ($_value->hour == $value->hour) {
                    $exsits = true;
                    $_data[] = $_value->count;
                }
            }
            if (!$exsits) {
                $_data[] = 0;
            }
        }
        return $_data;
    }

}