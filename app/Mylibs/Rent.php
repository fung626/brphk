<?php // Code within app\Helpers\Helper.php

namespace App\Mylibs;

use App\Mylibs\Common;

class Rent
{
    public static function withRentArrearsMonthProperties($rents)
    {
        $rentArrearsMonths = [];
        $headers = [
            __("User"),
            __("rent.property.owner"),
            __("rent.tenant"),
            __("rent.property.name"),
            __("rent.amount"),
            __("rent.startdate"),
            __("rent.fttdate"),
            __("rent.bcdate"),
        ];
        $tableHeaders = [
            ['text' => __("User"), 'value' => "users.name"],
            ['text' => __("rent.property.owner"), 'value' => "owner"],
            ['text' => __("rent.tenant"), 'value' => "tenant"],
            ['text' => __("rent.property.name"), 'value' => "property"],
            ['text' => __("rent.amount"), 'value' => "formatted_amount", 'sortable' => false],
            ['text' => __("rent.startdate"), 'value' => "start_date"],
            ['text' => __("rent.fttdate"), 'value' => "fix_term_tenancy_date"],
            ['text' => __("rent.bcdate"), 'value' => 'break_clause_date'],

        ];
        foreach ($rents as $item) {
            $item->formatted_amount = Common::formatPrice($item->amount);
            $item->actions = [
                [
                    'key' => 1,
                    'title' => "Details",
                    'color' => "primary",
                    'type' => "RouterPush",
                    'route' => "RentDetails",
                    'disabled' => false,
                ],
            ];
            $months = $item->rentArrearsMonths();
            $amounts = $item->rentArrearsMonthsAmount();
            $headers = array_unique(array_merge($headers, $months));
            $rentArrearsMonths = array_unique(array_merge($rentArrearsMonths, $months));
            foreach ($months as $month) {
                $found = false;
                $foundHeader = false;
                if (isset($amounts[$month])) {
                    $item->$month = Common::formatPrice($amounts[$month]);
                }
                foreach ($tableHeaders as $header) {
                    if ($header['text'] === $month) {
                        $found = true;
                    }
                }
                if (!$found) {
                    $tableHeaders[] = ['text' => $month, 'value' => $month, 'sortable' => false];
                }
            }
            // dd($item);
        }
        $tableHeaders[] = ['text' => __("Action"), 'value' => 'actions'];
        if (method_exists($rents, 'total') &&
            method_exists($rents, 'lastPage') &&
            method_exists($rents, 'hasMorePages')) {
            return [
                'months' => $rentArrearsMonths,
                'headers' => $tableHeaders,
                'data' => $rents->items(),
                'total' => $rents->total(),
                'last_page' => $rents->lastPage(),
                'has_more_pages' => $rents->hasMorePages(),
            ];
        }
        return [
            'months' => $rentArrearsMonths,
            'headers' => $headers,
            'data' => $rents,
        ];

    }
}