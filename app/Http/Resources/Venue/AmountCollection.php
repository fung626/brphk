<?php

namespace App\Http\Resources\Venue;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AmountCollection extends ResourceCollection
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
                'venue_item_id' => $item->venue_ie_item_id,
                'user_id' => $item->user_id,
                'type' => $item->type,
                'amount' => Common::formatPrice($item->amount),
                'date' => $item->date,
                'venue' => $item->venue,
                'item' => $item->item,
                'user' => $item->user,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'actions' => [
                    [
                        'key' => 1,
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