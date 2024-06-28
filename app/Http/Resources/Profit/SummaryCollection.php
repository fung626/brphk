<?php

namespace App\Http\Resources\Profit;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SummaryCollection extends ResourceCollection
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
            // Log::debug($item);
            return [
                'company' => $item->company,
                'profit' => Common::formatPrice($item->profit),
                'tax' => Common::formatPrice($item->tax),
            ];
        });
    }
}