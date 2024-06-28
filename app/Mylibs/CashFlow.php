<?php

namespace App\Mylibs;

use App\Models\Company\BankAccount;
use App\Models\Company\BankAccountBalance;
use App\Models\Users;
use App\Mylibs\Common;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Log;

class CashFlow
{

    public static function total($userId = false)
    {
        $sum = 0;
        $accounts = BankAccount::when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        })->get();
        foreach ($accounts as $account) {
            $result = BankAccountBalance::select('balance')
                ->where('company_bank_account_id', $account->id)
                ->orderBy('updated_at', 'DESC')
                ->first();
            if ($result && $result->balance) {
                $sum += $result->balance;
            }
        }
        // dd($result->toArray());
        return $sum;
    }

    public static function today($userId = false)
    {
        $result = BankAccountBalance::select(DB::raw('SUM(balance) AS sum'))
            ->whereDate('created_at', Carbon::today())
            ->when($userId, function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            })
            ->first();
        return $result->sum;
    }

    public static function daily($userIds, $range, $from, $to)
    {
        $users = Users::whereIn('id', $userIds)->get();
        $query = BankAccountBalance::select('user_id', DB::raw('SUM(balance) AS sum'), DB::raw('CAST(updated_at AS DATE) AS date'))
            ->whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$from, $to]);
        $query->groupBy('user_id', DB::raw('CAST(updated_at AS DATE)'));
        $balances = $query->get();

        $data = [];
        $index = 0;
        $dataIndex = 1;
        $sum = 0;

        foreach ($range as $date) {
            if ($index === 0) {
                $data[$index]['X1'] = __("date");
            }
            $data[$dataIndex]['X1'] = $date;
            $rowSum = 0;
            $subIndex = 2;
            foreach ($users as $user) {
                // $_user = $user;
                $found = false;
                if ($index === 0) {
                    $data[$index]['X' . $subIndex] = $user->name;
                }
                foreach ($balances as $balance) {
                    $_date = date("Y-m-d", strtotime($balance->date));
                    if ($balance->user_id === $user['id'] && $_date === $date) {
                        $data[$dataIndex]['X' . $subIndex] = Common::formatPrice($balance->sum);
                        $rowSum += $balance->sum;
                        $sum += $balance->sum;
                        $found = true;
                    }
                }
                if (!$found) {
                    $data[$dataIndex]['X' . $subIndex] = Common::formatPrice(0);
                }

                $subIndex++;
            }
            if ($index === 0) {
                $data[$index]['X' . $subIndex] = __('total');
            }
            $data[$dataIndex]['X' . $subIndex] = Common::formatPrice($rowSum);
            $dataIndex++;
            $index++;
        }

        $headers = [];
        $totalRow = [];
        $headerKeys = count($data) > 0 ? array_keys($data[0]) : [];

        foreach ($headerKeys as $key => $value) {
            $headers[] = ['text' => "", 'value' => __($value)];
            $tag = $key + 1;
            if ($key === count($headerKeys) - 2) {
                $totalRow['X' . $tag] = __('total') . ":";
            } else if ($key === count($headerKeys) - 1) {
                $totalRow['X' . $tag] = Common::formatPrice($sum);
            } else {
                $totalRow['X' . $tag] = "";
            }
        }

        $data[] = $totalRow;
        return [
            'headers' => $headers,
            'data' => $data,
            'total' => count($data),
        ];
    }

    public static function monthly($userIds, $range, $from, $to)
    {
        $users = Users::whereIn('id', $userIds)->get();
        $query = BankAccountBalance::select(
            'user_id',
            DB::raw('SUM(balance) AS sum'),
            DB::raw('YEAR(updated_at) as year'),
            DB::raw('MONTH(updated_at) as month')
        )
            ->whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('user_id', DB::raw('year'), DB::raw('month'));
        $balances = $query->get();

        $data = [];
        $index = 0;
        $dataIndex = 1;
        $sum = 0;

        foreach ($range as $date) {
            if ($index === 0) {
                $data[$index]['X1'] = __("date");
            }
            $data[$dataIndex]['X1'] = $date;
            $rowSum = 0;

            $subIndex = 2;
            foreach ($users as $user) {
                // $_user = $user;
                $found = false;
                if ($index === 0) {
                    $temp = $index + 1;
                    $data[$index]['X' . $subIndex] = $user->name;
                }

                foreach ($balances as $balance) {
                    $_date = sprintf("%04d-%02d", $balance->year, $balance->month);
                    if ($balance->user_id === $user['id'] && $_date === $date) {
                        // dd($balance, $_date, $date);
                        $data[$dataIndex]['X' . $subIndex] = Common::formatPrice($balance->sum);
                        $rowSum += $balance->sum;
                        $sum += $balance->sum;
                        $found = true;
                    }
                }
                if (!$found) {
                    $data[$dataIndex]['X' . $subIndex] = Common::formatPrice(0);
                }
                $subIndex++;
            }
            if ($index === 0) {
                $data[$index]['X' . $subIndex] = __('total');
            }
            $data[$dataIndex]['X' . $subIndex] = Common::formatPrice($rowSum);
            $dataIndex++;
            $index++;
        }

        $headers = [];
        $totalRow = [];
        $headerKeys = count($data) > 0 ? array_keys($data[0]) : [];
        foreach ($headerKeys as $key => $value) {
            $headers[] = ['text' => "", 'value' => __($value)];
            $tag = $key + 1;
            if ($key === count($headerKeys) - 2) {
                $totalRow['X' . $tag] = __('total') . ":";
            } else if ($key === count($headerKeys) - 1) {
                $totalRow['X' . $tag] = "$" . Common::formatPrice($sum);
            } else {
                $totalRow['X' . $tag] = "";
            }
        }

        $data[] = $totalRow;
        return [
            'headers' => $headers,
            'data' => $data,
            'total' => count($data),
        ];
    }

}
