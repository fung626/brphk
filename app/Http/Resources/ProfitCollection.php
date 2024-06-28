<?php

namespace App\Http\Resources;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ProfitCollection extends ResourceCollection
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
                'user_id' => $item->user_id,
                'company_id' => $item->company_id,
                'years' => $item->years,
                'amount' => Common::formatPrice($item->amount),
                'tax' => Common::formatPrice($item->tax),
                'remark' => $item->remark,
                'user' => $item->user,
                'company' => $item->company,
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