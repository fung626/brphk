<?php

namespace App\Models\User;

use App\Models\Users;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'user_notification';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'enable',
        'type',
    ];

    public function user()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

}