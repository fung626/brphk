<?php

namespace App\Http\Resources\Venue;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemCollection extends ResourceCollection
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
                'venue_id' => $item->venue_id,
                'user_id' => $item->user_id,
                'name' => $item->name,
                'venue' => $item->venue,
                'user' => $item->user,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'actions' => [
                    [
                        'key' => 1,
                        'title' => "Create Balance",
                        'color' => "info",
                        'type' => "RouterPush",
                        'disabled' => false,
                    ],
                    [
                        'key' => 2,
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