<?php

namespace App\Models\Venue;

use App\Models\Users;
use App\Models\Venue;
use App\Models\Venue\IEItem;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IEAmount extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'venue_ie_amount';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'venue_id',
        'venue_ie_item_id',
        'type',
        'amount',
        'date',
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function venue()
    {
        return $this->hasOne(Venue::class, 'id', 'venue_id');
    }

    public function item()
    {
        return $this->hasOne(IEItem::class, 'id', 'venue_ie_item_id');
    }

}