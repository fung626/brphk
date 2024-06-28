<?php

namespace App\Mylibs;

use App\Models\Profit as ProfitModel;
use App\Mylibs\Common;
use Illuminate\Support\Facades\DB;

// use Illuminate\Support\Facades\Log;

class Profit
{

    public static function summary($companyIds, $years)
    {
        $result = ProfitModel::with(['user', 'company'])
            ->select(
                'company_id',
                'years',
                DB::raw('SUM(amount) as profit'),
                DB::raw('SUM(tax) as tax')
            )
            ->when($companyIds, function ($query) use ($companyIds) {
                return $query->whereIn('company_id', $companyIds);
            })
            ->when($years, function ($query) use ($years) {
                return $query->whereIn('years', $years);
            })
            ->groupBy(DB::raw('company_id, years'))
            ->get();

        // dd($result->toArray());

        $data = [];
        $sum = [];
        $index = 0;

        foreach ($result as $item) {
            $found = false;
            $foundIndex = $index;
            if ($index === 0) {
                $data[$index]['X0'] = "id";
                $data[$index]['X1'] = __("Company");
                $sum['X0'] = "";
                $sum['X1'] = "";
            }
            foreach ($data as $key => $value) {
                if (isset($value['X0']) && $value['X0'] === $item->company_id) {
                    $found = true;
                    $foundIndex = $key;
                }
            }
            if (!$found) {
                $data[$index + 1]['X0'] = $item->company_id;
                $data[$index + 1]['X1'] = $item->company->name;
                $index++;
            }
        }
        // dd($data);
        $index = 0;
        foreach ($data as $value) {
            foreach ($result as $item) {
                $found = false;
                $foundIndex = null;
                $subIndex = 1;
                if (isset($value['X0']) && $value['X0'] === $item->company_id) {
                    $found = true;
                    $foundIndex = $index;
                }
                foreach ($years as $year) {
                    $profitLossIndex = $subIndex === 1 ? $subIndex + 1 : $subIndex * 2;
                    $taxIndex = $subIndex === 1 ? $subIndex + 2 : $subIndex * 2 + 1;
                    if ($index === 0) {
                        $data[$index]['X' . $profitLossIndex] = __("Profit") . "/" . __("Loss") . " " . $year;
                        $data[$index]['X' . $taxIndex] = $year . " " . __("Tax");
                    }
                    if ($found) {
                        if ($item->years === $year) {
                            // dd($item, $foundIndex, $data[$foundIndex], $profitLossIndex);
                            $data[$foundIndex]['X' . $profitLossIndex] = Common::formatPrice($item->profit);
                            $data[$foundIndex]['X' . $taxIndex] = Common::formatPrice($item->tax);
                            $sum['X' . $profitLossIndex] = isset($sum['X' . $profitLossIndex]) ? $sum['X' . $profitLossIndex] + $item->profit : $item->profit;
                            $sum['X' . $taxIndex] = isset($sum['X' . $taxIndex]) ? $sum['X' . $taxIndex] + $item->tax : $item->tax;
                        } else {
                            if (empty($data[$foundIndex]['X' . $profitLossIndex]) &&
                                empty($data[$foundIndex]['X' . $taxIndex])) {
                                $data[$foundIndex]['X' . $profitLossIndex] = Common::formatPrice(0);
                                $data[$foundIndex]['X' . $taxIndex] = Common::formatPrice(0);
                            }
                            if (empty($sum['X' . $profitLossIndex]) &&
                                empty($sum['X' . $taxIndex])) {
                                $sum['X' . $profitLossIndex] = 0;
                                $sum['X' . $taxIndex] = 0;
                            }
                        }
                    }
                    $subIndex++;
                }
            }
            $index++;
        }

        foreach ($sum as $key => $value) {
            if (is_numeric($sum[$key])) {
                $sum[$key] = Common::formatPrice($sum[$key]);
            }
        }

        $data[] = $sum;
        $headers = [];

        foreach (array_keys($data[0]) as $key => $value) {
            if ($key > 0) {
                $headers[] = ['text' => $value, 'value' => $value];
            }
        }

        return ['data' => $data, 'headers' => $headers, 'total' => count($data)];
    }

}