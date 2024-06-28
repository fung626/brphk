<?php

namespace App\Models\Schedule;

use App\Models\Schedule\Daily;
use App\Models\Schedule\Event;
use App\Models\Schedule\Monthly;
use App\Models\Schedule\Specific;
use App\Models\Schedule\Yearly;
use App\Models\Users;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'schedule';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
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

    public function daily()
    {
        return $this->hasMany(Daily::class, 'id', 'schedule_id');
    }

    public function monthly()
    {
        return $this->hasMany(Monthly::class, 'id', 'schedule_id');
    }

    public function yearly()
    {
        return $this->hasMany(Yearly::class, 'id', 'schedule_id');
    }

    public function specific()
    {
        return $this->hasMany(Specific::class, 'id', 'schedule_id');
    }

    public function event()
    {
        return $this->hasMany(Event::class, 'id', 'schedule_id');
    }

}