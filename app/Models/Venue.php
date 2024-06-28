<?php

namespace App\Models;

use App\Models\Users;
use App\Models\Venue\IEAmount;
use App\Models\Venue\IEItem;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'venue';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'remark',
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function items()
    {
        return $this->hasMany(IEItem::class, 'venue_id', 'id');
    }

    public function amounts()
    {
        return $this->hasMany(IEAmount::class, 'venue_id', 'id');
    }
}