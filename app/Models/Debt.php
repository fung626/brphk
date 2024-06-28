<?php

namespace App\Models;

use App\Models\Schedule\Schedule;
use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Debt extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'debt';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'schedule_id',
        'user_id',
        'type',
        'item',
        'amount',
        'paid',
        'due_date',
        'remark',
        'paid_date',
        'debt_date',
        'color',
        'updated_by',
    ];

    public function users()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function updatedBy()
    {
        return $this->hasOne(Users::class, 'id', 'updated_by');
    }

    public function schedule()
    {
        return $this->hasOne(Schedule::class, 'id', 'schedule_id');
    }

}
