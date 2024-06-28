<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'owner' => $this->owner,
            'tenant' => $this->tenant,
            'property' => $this->property,
            'amount' => $this->amount,
            'start_date' => $this->start_date,
            'fix_term_tenancy_date' => $this->fix_term_tenancy_date,
            'break_clause_date' => $this->break_clause_date,
            'remark' => $this->remark,
        ];
    }
}