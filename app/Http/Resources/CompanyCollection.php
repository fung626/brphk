<?php

namespace App\Http\Resources;

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
                'name_tc' => $item->name_tc,
                'name_en' => $item->name_en,
                'number' => $item->number,
                'secretary' => $item->secretary,
                'incorporation_date' => $item->incorporation_date,
                'address' => $item->address,
                'registered_share_capital' => $item->registered_share_capital,
                'share_holders' => $item->share_holders,
                'directors' => $item->directors,
                'owner' => $item->owner,
                'company' => $item,
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
