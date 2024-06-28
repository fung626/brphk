<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DebtEventCollection extends ResourceCollection
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
                'name' => $item->item,
                'start' => Carbon::parse($item->due_date)->toDateTimeString(),
                // 'end' => Carbon::parse($item->due_date)->addHours(12)->toDateTimeString(),
                'color' => $item->color,
                'timed' => false,
            ];
        });
    }
}