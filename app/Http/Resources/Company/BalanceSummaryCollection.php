<?php

namespace App\Http\Resources\Company;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;

// use Illuminate\Support\Facades\Log;

class BalanceSummaryCollection extends ResourceCollection
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
                'id' => $item->id,
                'user' => $item->user,
                'company' => $item->company,
                'bank_account' => $item->bank,
                'user_id' => $item->user_id,
                'date' => $item->updated_at,
                'balance' => Common::formatPrice($item->balance),
                'remark' => $item->remark,
            ];
        });
    }
}