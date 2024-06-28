<?php

namespace App\Models\Rent;

use App\Models\Rent;
use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rent_payment';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'rent_id',
        'payment_month',
        'payment_date',
        'amount',
        'remark',
    ];

    public function users()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function rent()
    {
        return $this->hasOne(Rent::class, 'id', 'rent_id');
    }

}