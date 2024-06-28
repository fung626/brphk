<?php

namespace App\Http\Resources;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class RentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return $this->collection->transform(function ($item) use ($request) {
            return [
                'id' => $item->id,
                'owner' => $item->owner,
                'tenant' => $item->tenant,
                'property' => $item->property,
                'amount' => Common::formatPrice($item->amount),
                'management_fee' => Common::formatPrice($item->management_fee),
                'rates' => Common::formatPrice($item->rates),
                'rent_per_square_foot' => Common::formatPrice($item->rent_per_square_foot),
                'government_rent' => Common::formatPrice($item->government_rent),
                'other_fee' => Common::formatPrice($item->other_fee),
                'area' => Common::formatNumber($item->area),
                'start_date' => $item->start_date,
                'fix_term_tenancy_date' => $item->fix_term_tenancy_date,
                'break_clause_date' => $item->break_clause_date,
                'remark' => $item->remark,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'users' => $item->users,
                'payments' => $item->payments,
                'actions' => [
                    [
                        'key' => 1,
                        'title' => "Details",
                        'color' => "primary",
                        'type' => "RouterPush",
                        'route' => "RentDetails",
                        'disabled' => false,
                    ],
                    [
                        'key' => 2,
                        'title' => "Rent Payment",
                        'color' => "info",
                        'type' => "RouterPush",
                        'route' => "CreateRentPayment",
                        'disabled' => false,
                    ],
                    [
                        'key' => 3,
                        'title' => "Delete",
                        'color' => "danger",
                        'type' => "Delete",
                        'disabled' => Auth::user()->role === "admin" ? false : true,
                    ],
                ],
            ];
        });
    }
}