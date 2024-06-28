<?php

namespace App\Http\Resources;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class DebtCollection extends ResourceCollection
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
                'schedule_id' => $item->schedule_id,
                'user_id' => $item->user_id,
                'item' => $item->item,
                'amount' => Common::formatPrice($item->amount),
                'paid' => Common::formatPrice($item->paid),
                'due_date' => $item->due_date,
                'remark' => $item->remark,
                'paid_date' => $item->paid_date,
                'debt_date' => $item->debt_date,
                'schedule' => $item->schedule,
                'users' => $item->users,
                'updated_by' => $item->updatedBy,
                'debt' => $item,
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