<?php

namespace App\Models;

use App\Models\Rent\Payment;
use App\Models\Users;
use App\Mylibs\Common;
use Carbon\Carbon;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Rent extends Model
{
    use HasFactory, Uuid, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rent';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'owner',
        'tenant',
        'property',
        'amount',
        'management_fee',
        'rates',
        'rent_per_square_foot',
        'government_rent',
        'other_fee',
        'area',
        'start_date',
        'fix_term_tenancy_date',
        'break_clause_date',
        'remark',
        'updated_by',
    ];

    // protected $appends = ['has_rent_arrears'];

    public static function hasRentArrears()
    {
        $result = self::get();
        $ids = [];
        foreach ($result as $item) {
            if (count($item->rentArrearsMonths()) > 0) {
                $ids[] = $item->id;
            }
        }
        return self::with(['users', 'updatedBy', 'payments'])
            ->select('rent.*', DB::raw('UUID() as "key"'))
            ->whereIn('rent.id', $ids)
            ->leftJoin('users', 'users.id', '=', 'rent.user_id')
            ->leftJoin('rent_payment', 'rent_payment.rent_id', '=', 'rent.id')
            ->groupBy('rent.id');
    }

    public function rentArrearsMonthsAmount()
    {
        $end_date = $this->fix_term_tenancy_date;
        $carbon = Carbon::parse($end_date);
        $end_date = $carbon->isFuture() ? date('Y-m-d') : $this->fix_term_tenancy_date;
        $months = Common::getMonthsFromRange($this->start_date, $end_date);
        $payments = Payment::where([
            'rent_id' => $this->id,
        ])->whereIn('payment_month', $months)
            ->get();
        $data = [];
        foreach ($months as $month) {
            $paid = 0;
            foreach ($payments as $payment) {
                if ($payment->payment_month === $month) {
                    $paid += $payment->amount;
                }
            }
            if ($paid < $this->amount) {
                // Log::debug($this->amount * 1);
                $data[$month] = $this->amount - $paid;
            }
        }
        return $data;
    }

    public function rentArrearsMonths()
    {
        $end_date = $this->fix_term_tenancy_date;
        $carbon = Carbon::parse($end_date);
        $end_date = $carbon->isFuture() ? date('Y-m-d') : $this->fix_term_tenancy_date;
        $months = Common::getMonthsFromRange($this->start_date, $end_date);
        $payments = Payment::where([
            'rent_id' => $this->id,
        ])->whereIn('payment_month', $months)
            ->get();
        // foreach ($payments as $payment) {
        //     $index = 0;
        //     foreach ($months as $month) {
        //         if ($payment->payment_month === $month) {
        //             unset($months[$index]);
        //         }
        //         $index++;
        //     }
        // }
        foreach ($months as $month) {
            $paid = 0;
            $index = 0;
            foreach ($payments as $payment) {
                if ($payment->payment_month === $month) {
                    $paid += $payment->amount;
                }
            }
            if ($paid >= $this->amount) {
                // Log::debug($this->amount * 1);
                unset($months[$index]);
            }
            $index++;
        }
        return $months;
    }

    public function users()
    {
        return $this->hasOne(Users::class, 'id', 'user_id');
    }

    public function updatedBy()
    {
        return $this->hasOne(Users::class, 'id', 'updated_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'rent_id', 'id');
    }
}
