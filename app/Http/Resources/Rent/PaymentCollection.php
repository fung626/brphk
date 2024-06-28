<?php

namespace App\Http\Resources\Rent;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class PaymentCollection extends ResourceCollection
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
                'amount' => Common::formatPrice($item->amount),
                'payment_month' => $item->payment_month,
                'payment_date' => $item->payment_date,
                'remark' => $item->remark,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'users' => $item->users,
                'rent' => $item->rent,
                'actions' => [
                    [
                        'key' => 1,
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