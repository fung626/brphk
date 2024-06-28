<?php

namespace App\Http\Resources\Company;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Auth;

class BankAccountCollection extends ResourceCollection
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
                'owner_user_id' => $item->owner_user_id,
                'company_id' => $item->company_id,
                'owner' => $item->owner,
                'company' => $item->company,
                'bank' => $item->bank,
                'account_type' => $item->account_type,
                'company_bank_account' => $item,
                'actions' => [
                    [
                        'key' => 1,
                        'title' => "Details",
                        'color' => "primary",
                        'type' => "RouterPush",
                        'route' => "CompanyBankDetails",
                        'disabled' => false,
                    ],
                    [
                        'key' => 2,
                        'title' => "Create Balance",
                        'color' => "info",
                        'type' => "RouterPush",
                        'route' => "CreateCompanyBankBalance",
                        'disabled' => false,
                    ],
                    [
                        'key' => 3,
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
