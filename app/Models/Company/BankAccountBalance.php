<?php

namespace App\Models\Company;

use App\Models\Company;
use App\Models\Company\BankAccount;
use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccountBalance extends Model
{
    use HasFactory, Uuid;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company_bank_account_balance';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'company_bank_account_id',
        'balance',
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

    public function company()
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function bank()
    {
        return $this->hasOne(BankAccount::class, 'id', 'company_bank_account_id');
    }

}