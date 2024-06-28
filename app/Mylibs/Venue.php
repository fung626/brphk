<?php

namespace App\Mylibs;

use App\Models\Venue as VenueModel;
use App\Models\Venue\IEAmount as VenueAmountModel;
use App\Models\Venue\IEItem as VenueItemModel;
use App\Mylibs\Common;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Venue
{

    public static function summary($venueIds, $months)
    {
        $migrated = [];
        $first = self::getFormarttedSummary($venueIds, $months, 'income');
        $second = self::getFormarttedSummary($venueIds, $months, 'expenditure');

        for ($i = 0; $i < count($first); $i++) {
            $temp = $first[$i];
            // $sub = $second [$i];
            if (count($second) > 0) {
                for ($y = 0; $y < count($second[$i]) - 1; $y++) {
                    $length = count($first[$i]);
                    $indexKey = $length + $y;
                    $valueKey = $y + ($i === 0 ? 0 : 1);
                    $temp['X' . $indexKey] = $second[$i]['X' . $valueKey];
                }
            }

            $profitIndexKey = count($temp);
            $temp['X' . $profitIndexKey] = null;
            if ($i === 1) {
                $temp['X' . $profitIndexKey] = __('Profit');
            }
            if ($i > 1) {
                Log::debug(count($second) > 0 ? count($second[$i]) - 1 : 0);
                $firstTotalKey = count($first[$i]) - 1;
                $secondTotalKey = count($second) > 0 ? count($second[$i]) - 1 : false;
                $temp['X' . $profitIndexKey] = $first[$i]['X' . $firstTotalKey] - ($secondTotalKey ? $second[$i]['X' . $secondTotalKey] : 0);
            }
            $migrated[] = $temp;
        }

        for ($i = 0; $i < count($migrated); $i++) {
            foreach ($migrated[$i] as $key => $value) {
                if (is_numeric($value)) {
                    $migrated[$i][$key] = "$ " . Common::formatPrice($value);
                }
            }
        }

        $headers = [];

        if (count($migrated) > 0) {
            foreach (array_keys($migrated[0]) as $key => $value) {
                $headers[] = ['text' => $value, 'value' => $value];
            }
        }

        return ['data' => $migrated, 'headers' => $headers, 'total' => count($migrated)];

    }

    public static function getFormarttedSummary($venueIds, $months, $type = null)
    {

        $amounts = VenueAmountModel::with(['user', 'venue', 'item'])
            ->select(
                'venue_id',
                'venue_ie_item_id',
                'type',
                DB::raw('SUM(amount) AS sum'),
                DB::raw('YEAR(date) as year'),
                DB::raw('MONTH(date) as month'),
            )
            ->when($venueIds, function ($query) use ($venueIds) {
                return $query->whereIn('venue_id', $venueIds);
            })
            ->when($type, function ($query) use ($type) {
                return $query->where('type', $type);
            })
            ->groupBy(DB::raw('venue_id,venue_ie_item_id,type,year,month'))
            ->orderBy(DB::raw('venue_id,venue_ie_item_id,year,month'))
            ->get();

        $venues = VenueModel::with(['user', 'items', 'amounts'])
            ->when($type, function ($query) use ($type) {
                $query->whereHas('amounts', function ($query) use ($type) {
                    $query->where('type', $type);
                });
            })
            ->when($venueIds, function ($query) use ($venueIds) {
                return $query->whereIn('id', $venueIds);
            })->get();

        // dd($venues->toArray());

        $data = [];
        $temp = [];
        $sum = [];
        $index = 0;

        foreach ($venues as $venue) {

            $subIndex = 0;
            // $items = $venue->items;
            $items = VenueItemModel::with(['amounts'])
                ->when($venueIds, function ($query) use ($venueIds) {
                    return $query->whereIn('venue_id', $venueIds);
                })
                ->whereHas('amounts', function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->get();
            foreach ($months as $month) {
                $exploded = explode("-", $month);
                $explodedYear = $exploded[0] * 1;
                $explodedMonth = $exploded[1] * 1;
                $lenght = count($items) + 2;
                $firstSubIndex = $subIndex + 1;
                $sencondSubIndex = $subIndex + 2;
                $totalKey = count($items) + 1;

                if ($subIndex === 0) {
                    $temp[$subIndex]['X' . $subIndex] = $venue->name . ' － ' . __(ucfirst($type));
                    $temp[$firstSubIndex]['X' . $subIndex] = __('Month');
                    $sum['X' . $subIndex] = "";
                    foreach ($items as $key => $item) {
                        $_key = $key + 1;
                        $temp[$subIndex]['X' . $_key] = null;
                        $temp[$firstSubIndex]['X' . $_key] = $item->name;
                        if ($_key === count($items)) {
                            $totalKey = $_key + 1;
                            $temp[$subIndex]['X' . $totalKey] = null;
                            $temp[$firstSubIndex]['X' . $totalKey] = __('total');
                        }
                    }
                }

                $temp[$sencondSubIndex]['X0'] = $month;
                foreach ($items as $key => $item) {
                    $found = false;
                    $_key = $key + 1;
                    foreach ($amounts as $amount) {
                        if ($amount->year === $explodedYear &&
                            $amount->month === $explodedMonth &&
                            $amount->item->id === $item->id) {
                            $temp[$sencondSubIndex]['X' . $_key] = $amount->sum;
                            $found = true;
                        }
                    }
                    if (!$found) {
                        $temp[$sencondSubIndex]['X' . $_key] = 0;
                    }

                    $totalKey = count($items) + 1;
                    if (!isset($temp[$sencondSubIndex]['X' . $totalKey])) {
                        $temp[$sencondSubIndex]['X' . $totalKey] = $temp[$sencondSubIndex]['X' . $_key];
                    } else {
                        $temp[$sencondSubIndex]['X' . $totalKey] = $temp[$sencondSubIndex]['X' . $totalKey] + $temp[$sencondSubIndex]['X' . $_key];
                    }
                    if (!isset($sum['X' . $_key])) {
                        $sum['X' . $_key] = $temp[$sencondSubIndex]['X' . $_key];
                    } else {
                        $sum['X' . $_key] = $sum['X' . $_key] + $temp[$sencondSubIndex]['X' . $_key];
                    }
                    if (!isset($sum['X' . $totalKey])) {
                        $sum['X' . $totalKey] = $temp[$sencondSubIndex]['X' . $_key];
                    } else {
                        $sum['X' . $totalKey] += $temp[$sencondSubIndex]['X' . $_key];
                    }

                }
                $subIndex++;
            }
            $temp[] = $sum;
            $index++;
        }

        $data = $temp;

        return $data;
    }

}