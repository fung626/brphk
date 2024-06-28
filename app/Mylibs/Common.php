<?php // Code within app\Helpers\Helper.php

namespace App\Mylibs;

use App\Models\Users;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;

class Common
{

    public static function IsNullOrEmpty($value)
    {
        return (!isset($value) || empty($value) || trim($value) === '');
    }

    public static function IsNumNullOrEmpty($value)
    {
        return !(is_numeric($value) && !is_null($value));
    }

    // public static function userId()
    // {

    //     return mt_rand(100, 999) . mt_rand(100000, 999999) . mt_rand(100, 999);
    // }

    public static function userId($length = 8)
    {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $characters[rand(0, $charactersLength - 1)];
        }
        $exist = Users::where(['id' => $rand])->first();
        if ($exist) {
            return self::userId();
        }
        return $rand;
    }

    public static function securityCode($digits = 6)
    {
        return rand(pow(10, $digits - 1), pow(10, $digits) - 1);
    }

    public static function userIP()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }
        return $ip;
    }

    public static function maskEmail($email, $mask_character = '*')
    {
        if ($email) {
            $em = explode("@", $email);
            $name = implode('@', array_slice($em, 0, count($em) - 1));
            $len = floor(strlen($name) / 2);
            return substr($name, 0, $len) . str_repeat($mask_character, $len) . "@" . end($em);
        }
        return null;
    }

    public static function maskPhone($phone, $trim = 4, $mask_character = '*')
    {
        $str = "";
        $suffix = substr($phone, strlen($phone) - $trim, $trim);
        $prefix = substr($phone, 0, -$trim);

        for ($x = 0; $x < strlen($prefix); $x++):
            $str .= (is_numeric($prefix[$x])) ? str_replace($prefix[$x], $mask_character, $prefix[$x]) : $prefix[$x];
        endfor;

        return $str . $suffix;
    }

    public static function replaceEmailString($email = "", $replacement = "")
    {
        return substr($email, 0, strpos($email, '@'));
    }

    public static function contains($str, $subString)
    {
        return strpos($str, $subString) !== false;
    }

    public static function formatPrice($value = 0.00, $default_decimal = 2, $symbol = true)
    {
        $value = $value * 1;
        // $count = strlen(substr(strrchr((float) $value, "."), 1));
        // $count = strlen(substr(strrchr(sprintf('%f', $value), "."), 1));
        $count = self::numberOfDecimals($value);
        // $places = $count > $default_decimal ? $count : $default_decimal;
        $places = $default_decimal;
        $_symbol = $symbol ? '$' : '';
        return $_symbol . number_format($value, $places);
    }

    public static function formatNumber($value = 0.00, $default_decimal = 2)
    {
        $value = $value * 1;
        // $count = strlen(substr(strrchr((float) $value, "."), 1));
        // $count = strlen(substr(strrchr(sprintf('%f', $value), "."), 1));
        $count = self::numberOfDecimals($value);
        $places = $count > $default_decimal ? $count : $default_decimal;
        return number_format($value, $places);
    }

    public static function numberOfDecimals($value)
    {
        if ((int) $value == $value) {
            return 0;
        } else if (!is_numeric($value)) {
            return false;
        }
        // return strlen($value) - strrpos($value, '.') - 1;
        $value = floatval($value);
        $count = 0;
        for ($count = 0; $value != round($value, $count); $count++);
        return $count;
    }

    public static function shortenNumber($number, $precision = 3, $divisors = null)
    {
        // Setup default $divisors if not provided
        if (!isset($divisors)) {
            $divisors = array(
                pow(1000, 0) => '', // 1000^0 == 1
                pow(1000, 1) => 'K', // Thousand
                pow(1000, 2) => 'M', // Million
                pow(1000, 3) => 'B', // Billion
                pow(1000, 4) => 'T', // Trillion
                pow(1000, 5) => 'Qa', // Quadrillion
                pow(1000, 6) => 'Qi', // Quintillion
            );
        }

        // Loop through each $divisor and find the
        // lowest amount that matches
        foreach ($divisors as $divisor => $shorthand) {
            if (abs($number) < ($divisor * 1000)) {
                // We found a match!
                break;
            }
        }

        // We found our match, or there were no matches.
        // Either way, use the last defined value for $divisor.
        return number_format($number / $divisor, $precision) . $shorthand;
    }

    public static function roundUpToAny($n, $x = 5)
    {
        return round(($n + $x / 2) / $x) * $x;
    }

    /*
    Time Related
     */

    public static function datetime(
        $datetime,
        $timezone = 'Asia/Hong_Kong',
        $format = 'Y-m-d H:i:s'
    ) {
        $carbon = Carbon::createFromFormat($format, $datetime, $timezone);
        // $carbon = Carbon::createFromFormat($format, $datetime, 'UTC');
        // $carbon->setTimezone($timezone);
        return $carbon->toDateTimeString();
    }

    public static function now(
        $timezone = 'Asia/Hong_Kong',
        $format = 'Y-m-d H:i:s'
    ) {
        $carbon = Carbon::now($timezone);
        return $carbon->format($format);
    }

    public static function formatTime(
        $time = '00:00:00',
        $format = 'H:i'
    ) {
        $time = new \DateTime($time);
        return $time->format($format);
    }

    public static function timeAgo($time_ago)
    {
        $time_ago = strtotime($time_ago);
        $cur_time = time();
        $time_elapsed = $cur_time - $time_ago;
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60);
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86400);
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640);
        $years = round($time_elapsed / 31207680);
        // Seconds
        if ($seconds <= 60) {
            return "just now";
        } else if ($minutes <= 60) { //Minutes
            if ($minutes == 1) {
                return "one minute ago";
            } else {
                return "$minutes minutes ago";
            }
        } else if ($hours <= 24) { //Hours
            if ($hours == 1) {
                return "an hour ago";
            } else {
                return "$hours hrs ago";
            }
        } else if ($days <= 7) { //Days
            if ($days == 1) {
                return "yesterday";
            } else {
                return "$days days ago";
            }
        } else if ($weeks <= 4.3) { //Weeks
            if ($weeks == 1) {
                return "a week ago";
            } else {
                return "$weeks weeks ago";
            }
        } else if ($months <= 12) { //Months
            if ($months == 1) {
                return "a month ago";
            } else {
                return "$months months ago";
            }
        } else { //Years
            if ($years == 1) {
                return "one year ago";
            } else {
                return "$years years ago";
            }
        }
    }

    public static function getValidDate($date, $format = 'Y-m-d')
    {
        // dd(Carbon::createFromFormat($format, $date)->format('Y-m-d'));
        if (Carbon::createFromFormat($format, $date)->format($format) === $date) {
            return $date;
        }
        list($y, $m, $d) = explode('-', $date);
        if (!$d || $d <= 1) {
            return false;
        }
        $d = $d - 1;
        return self::getValidDate($y . '-' . $m . '-' . $d);
    }

    public static function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = [];
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }

    public static function getWeekDayDatesFromRange($start, $end, $weekday, $format = 'Y-m-d')
    {
        $start = new DateTime($start); // Start date
        $end = new DateTime($end); // Create a datetime object from your Carbon object
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period

        $dates = [];
        foreach ($period as $dt) {
            if ($dt->format('l') === $weekday) {
                $dates[] = $dt->format($format);
            }
        }

        return $dates;
    }

    public static function getMonthsFromRange($start, $end)
    {
        $start = new DateTime($start); // Start date
        $end = new DateTime($end); // Create a datetime object from your Carbon object
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period

        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("Y-m");
        }

        return $months;
    }

    public static function getYearsFromRange($start, $end)
    {
        $start = new DateTime($start); // Start date
        $end = new DateTime($end); // Create a datetime object from your Carbon object
        $interval = DateInterval::createFromDateString('1 year');
        $period = new DatePeriod($start, $interval, $end); // Get a set of date beetween the 2 period

        $months = [];
        foreach ($period as $dt) {
            $months[] = $dt->format("Y");
        }

        return $months;
    }

    public static function formatBytes($bytes, $to = 'GB', $decimal_places = 2)
    {
        $formulas = [
            'KB' => number_format($bytes / 1024, $decimal_places),
            'MB' => number_format($bytes / 1048576, $decimal_places),
            'GB' => number_format($bytes / 1073741824, $decimal_places),
        ];
        return isset($formulas[$to]) ? $formulas[$to] . ' ' . $to : 0 . ' ' . $to;
    }

    // public static function systemMemInfo()
    // {
    //     $data = explode("\n", file_get_contents("/proc/meminfo"));
    //     $meminfo = array();
    //     foreach ($data as $line) {
    //         list($key, $val) = explode(":", $line);
    //         $meminfo[$key] = trim($val);
    //     }
    //     return $meminfo;
    // }

}