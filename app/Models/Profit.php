<?php

namespace App\Models;

use App\Models\Profit\Company;
use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profit extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profit';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'company_id',
        'years',
        'amount',
        'tax',
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

}