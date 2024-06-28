<?php

namespace App\Http\Resources;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class TaxCollection extends ResourceCollection
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
                'company' => $item->company,
                'item' => $item->item,
                'amount' => Common::formatPrice($item->amount),
                'date' => $item->date,
                'remark' => $item->remark,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'users' => $item->users,
                'actions' => [
                    [
                        'key' => 1,
                        'title' => "Details",
                        'color' => "primary",
                        'type' => "RouterPush",
                        'disabled' => false,
                    ],
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