<?php

namespace App\Models;

use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expenses extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'expenses';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'cheque_bank',
        'cheque_no',
        'cheque_issuer',
        'signer',
        'amount',
        'issued_company',
        'pay_to',
        'cheque_issued_date',
        'internal_transfer',
        'item',
        'remark',
        'updated_by',
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function updatedBy()
    {
        return $this->hasOne(Users::class, 'id', 'updated_by');
    }

}
