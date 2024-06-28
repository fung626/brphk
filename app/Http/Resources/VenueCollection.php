<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VenueCollection extends ResourceCollection
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
                'remark' => $item->remark,
                'user' => $item->user,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'actions' => [
                    [
                        'key' => 1,
                        'title' => "Create Item",
                        'color' => "info",
                        'type' => "RouterPush",
                        'route' => "CreateVenueItem",
                        'disabled' => false,
                    ],
                    [
                        'key' => 2,
                        'title' => "Create Amount",
                        'color' => "info",
                        'type' => "RouterPush",
                        'route' => "CreateVenueItemAmount",
                        'disabled' => false,
                    ],
                    [
                        'key' => 3,
                        'title' => "Details",
                        'color' => "primary",
                        'type' => "RouterPush",
                        'route' => "VenueDetails",
                        'disabled' => false,
                    ],
                    [
                        'key' => 4,
                        'title' => "Delete",
                        'color' => "danger",
                        'type' => "Delete",
                        'disabled' => false,
                    ],
                ],
            ];
        });
    }
}