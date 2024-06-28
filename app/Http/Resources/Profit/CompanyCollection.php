<?php

namespace App\Http\Resources\Profit;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class CompanyCollection extends ResourceCollection
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
                'name' => $item->name,
                'user' => $item->user,
                'remark' => $item->remark,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
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