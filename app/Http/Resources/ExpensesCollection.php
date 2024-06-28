<?php

namespace App\Http\Resources;

use App\Mylibs\Common;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class ExpensesCollection extends ResourceCollection
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
                'cheque_bank' => $item->cheque_bank,
                'cheque_no' => $item->cheque_no,
                'cheque_issuer' => $item->cheque_issuer,
                'signer' => $item->signer,
                'amount' => Common::formatPrice($item->amount),
                'issued_company' => $item->issued_company,
                'pay_to' => $item->pay_to,
                'cheque_issued_date' => $item->cheque_issued_date,
                'internal_transfer' => $item->internal_transfer === 1 ? __('Yes') : __('No'),
                'item' => $item->item,
                'remark' => $item->remark,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,
                'user' => $item->user,
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
