<?php

namespace App\Models;

use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'company';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'owner_user_id',
        'user_id',
        'name_tc',
        'name_en',
        'number',
        'secretary',
        'incorporation_date',
        'address',
        'registered_share_capital',
        'share_holders',
        'directors',
        'updated_by',
    ];

    protected $casts = [
        'share_holders' => 'array',
        'directors' => 'array',
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function updatedBy()
    {
        return $this->hasOne(Users::class, 'id', 'updated_by');
    }

    public function owner()
    {
        return $this->hasOne(Users::class, 'id', 'owner_user_id');
    }

}