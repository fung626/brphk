<?php

namespace App\Http\Resources\Company;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class BankAccountBalanceCollection extends ResourceCollection
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
                'user' => $item->user,
                'company' => $item->company,
                'bank_account' => $item->bank,
                'user_id' => $item->user_id,
                'balance' => Common::formatPrice($item->balance),
                'remark' => $item->remark,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'actions' => [
                    // [
                    //     'key' => 1,
                    //     'title' => "Details",
                    //     'color' => "primary",
                    //     'type' => "RouterPush",
                    //     'disabled' => false,
                    // ],
                    [
                        'key' => 2,
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