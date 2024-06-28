<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\DebtCollection;
use App\Http\Resources\DebtEventCollection;
use App\Models\Debt;
use App\Models\Schedule\Daily as ScheduleDaily;
use App\Models\Schedule\Event as ScheduleEvent;
use App\Models\Schedule\Monthly as ScheduleMonthly;
use App\Models\Schedule\Schedule;
use App\Models\Schedule\Specific as ScheduleSpecific;
use App\Models\Schedule\Weekly as ScheduleWeekly;
use App\Models\Schedule\Yearly as ScheduleYearly;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Validator;

class DebtController extends Controller
{
    //

    public function get(Request $request)
    {
        $query = Debt::with(['users', 'schedule', 'updatedBy'])
            ->select('debt.*')
            ->has('schedule')
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['type']), function ($query) {
                return $query->where('debt.type', request('type'));
            })
            ->when($request->filled(['from_date', 'to_date']), function ($query) {
                $from = date(request('from_date'));
                $to = date(request('to_date'));
                return $query->whereBetween('due_date', [$from, $to]);;
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('debt.id', 'like', '%' . $keyword . '%')
                        ->orWhere('item', 'like', '%' . $keyword . '%');
                });
            })
            ->leftJoin('users', 'users.id', '=', 'debt.user_id')
            ->leftJoin('users as updated_by', 'users.id', '=', 'debt.updated_by')
            ->groupBy('debt.id');

        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy !== "action") {
                    $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
                }
                $index++;
            }
        }

        $debtAmount = $query->sum('amount');
        $paidAmont = $query->sum('paid');
        $totalAmont = $debtAmount - $paidAmont;
        $response = config('response.common.success');
        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
            $resource = new DebtCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
                'debt_amount' => Common::formatPrice($debtAmount),
                'paid_amount' => Common::formatPrice($paidAmont),
                'total_amount' => Common::formatPrice($totalAmont),
            ];
        } else {
            $result = $query->get();
            $resource = new DebtCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function events(Request $request)
    {
        $result = Debt::with(['users', 'schedule'])
            ->has('schedule')
            ->when($request->filled(['from_date', 'to_date']), function ($query) {
                $from = date(request('from_date'));
                $to = date(request('to_date'));
                return $query->whereBetween('due_date', [$from, $to]);;
            })->get();

        $resource = new DebtEventCollection($result);
        $response = config('response.common.success');
        $response['data'] = $resource;
        return response()->json($response, 200);
    }

    public function details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:debt,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $result = Debt::with(['users', 'schedule'])
            ->has('schedule')
            ->where(['id' => request('id')])
            ->first();

        $response = config('response.common.success');
        $response['data'] = $result;
        return response()->json($response, 200);

    }

    public function post(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'item' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'schedule.type' => 'required|in:daily,weekly,monthly,yearly,specific',
            //
            'due_date.specific.date' => request('schedule.type') === "specific" ? 'required_if:schedule.type,==,specific|date_format:Y-m-d' : '',
            'due_date.daily.time' => request('schedule.type') === "daily" ? 'required_if:schedule.type,==,daily' : '',
            'due_date.weekly.day' => request('schedule.type') === "weekly" ? 'required_if:schedule.type,==,weekly' : '',
            'due_date.monthly.date' => request('schedule.type') === "monthly" ? 'required_if:schedule.type,==,monthly|date_format:m-d' : '',
            'due_date.yearly.date' => request('schedule.type') === "yearly" ? 'required_if:schedule.type,==,yearly|date_format:m-d' : '',
            //
            'schedule.start_date.date' => request('schedule.type') !== "specific" ? 'required|date|date_format:Y-m-d' : '',
            'schedule.end_date.date' => request('schedule.type') !== "specific" ? 'required|date|date_format:Y-m-d|after:schedule.start_date.date' : '',
            'schedule.specific.date' => request('schedule.type') === "specific" ? 'required_if:schedule.type,==,specific|date_format:Y-m-d|before:due_date.specific.date' : '',
            'schedule.daily.time' => request('schedule.type') === "daily" ? 'required_if:schedule.type,==,daily|before:due_date.daily.time' : '',
            'schedule.weekly.day' => request('schedule.type') === "weekly" ? 'required_if:schedule.type,==,weekly|before_weekday:' . request('due_date.weekly.day') . '' : '',
            'schedule.monthly.date' => request('schedule.type') === "monthly" ? 'required_if:schedule.type,==,monthly|date_format:m-d|before:due_date.monthly.date' : '',
            'schedule.yearly.date' => request('schedule.type') === "yearly" ? 'required_if:schedule.type,==,yearly|date_format:m-d|before:due_date.yearly.date' : '',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                $scheduleType = request('schedule.type');

                $schedule = Schedule::create([
                    'user_id' => $user->id,
                    'type' => request('schedule.type'),
                    'start_date' => request('schedule.start_date.date'),
                    'end_date' => request('schedule.end_date.date'),
                ]);

                switch ($scheduleType) {
                    case 'daily':
                        ScheduleDaily::create([
                            'schedule_id' => $schedule->id,
                            'time_of_day' => request('schedule.daily.time'),
                        ]);

                        $dateRange = Common::getDatesFromRange(request('schedule.start_date.date'), request('schedule.end_date.date'));
                        $data = [];
                        foreach ($dateRange as $date) {
                            // 2021-04-16 05:26:07
                            $data[] = [
                                'id' => Str::uuid()->toString(),
                                'schedule_id' => $schedule->id,
                                'user_id' => $user->id,
                                'type' => request('type'),
                                'item' => request('item'),
                                'amount' => request('amount'),
                                'remark' => request('remark'),
                                'color' => $request->filled('color') ? request('color') : '#00BCD0FF',
                                'due_date' => $date . ' ' . request('due_date.daily.time'),
                                'debt_date' => $date . ' ' . request('schedule.daily.time'),
                                'updated_by' => $user->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                        Debt::insert($data);
                        break;
                    case 'weekly':
                        $scheduleDateRange = Common::getWeekDayDatesFromRange(
                            request('schedule.start_date.date'),
                            request('schedule.end_date.date'),
                            request('schedule.weekly.day')
                        );
                        if (count($scheduleDateRange) > 0) {

                            $scheduleDayOfWeek = date('N', strtotime(request('schedule.weekly.day')));
                            $dueDayOfWeek = date('N', strtotime(request('due_date.weekly.day')));

                            $dueDateRange = Common::getWeekDayDatesFromRange(
                                $scheduleDateRange[0],
                                request('schedule.end_date.date'),
                                request('due_date.weekly.day')
                            );

                            ScheduleWeekly::create([
                                'schedule_id' => $schedule->id,
                                'start_day_of_week' => $scheduleDayOfWeek,
                                'end_day_of_week' => $dueDayOfWeek,
                            ]);

                            $data = [];
                            foreach ($scheduleDateRange as $key => $scheduleDate) {
                                if (count($dueDateRange) > $key) {
                                    $dueDate = $dueDateRange[$key];
                                    $data[] = [
                                        'id' => Str::uuid()->toString(),
                                        'schedule_id' => $schedule->id,
                                        'user_id' => $user->id,
                                        'type' => request('type'),
                                        'item' => request('item'),
                                        'amount' => request('amount'),
                                        'remark' => request('remark'),
                                        'color' => $request->filled('color') ? request('color') : '#00BCD0FF',
                                        'due_date' => $dueDate,
                                        'debt_date' => $scheduleDate,
                                        'updated_by' => $user->id,
                                        'updated_at' => date('Y-m-d H:i:s'),
                                        'created_at' => date('Y-m-d H:i:s'),
                                    ];
                                }
                            }
                            Debt::insert($data);
                        }
                        break;
                    case 'monthly':
                        ScheduleMonthly::create([
                            'schedule_id' => $schedule->id,
                            'day_of_month' => request('schedule.monthly.day_of_month'),
                        ]);

                        $monthRange = Common::getMonthsFromRange(request('schedule.start_date.date'), request('schedule.end_date.date'));
                        $data = [];
                        foreach ($monthRange as $month) {
                            $dueDate = Common::getValidDate($month . '-' . request('due_date.monthly.day_of_month'));
                            $debtDate = Common::getValidDate($month . '-' . request('schedule.monthly.day_of_month'));
                            $data[] = [
                                'id' => Str::uuid()->toString(),
                                'schedule_id' => $schedule->id,
                                'user_id' => $user->id,
                                'type' => request('type'),
                                'item' => request('item'),
                                'amount' => request('amount'),
                                'remark' => request('remark'),
                                'color' => $request->filled('color') ? request('color') : '#00BCD0FF',
                                'due_date' => $dueDate,
                                'debt_date' => $debtDate,
                                'updated_by' => $user->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        Debt::insert($data);
                        break;
                    case 'yearly':
                        ScheduleYearly::create([
                            'schedule_id' => $schedule->id,
                            'day_of_year' => request('schedule.yearly.month_of_year') . '-' . request('schedule.yearly.day_of_year'),
                        ]);

                        $yearRange = Common::getYearsFromRange(request('schedule.start_date.date'), request('schedule.end_date.date'));
                        foreach ($yearRange as $year) {
                            $dueDate = Common::getValidDate($year . '-' . request('due_date.yearly.date'));
                            $debtDate = Common::getValidDate($year . '-' . request('schedule.yearly.date'));
                            $data[] = [
                                'id' => Str::uuid()->toString(),
                                'schedule_id' => $schedule->id,
                                'user_id' => $user->id,
                                'type' => request('type'),
                                'item' => request('item'),
                                'amount' => request('amount'),
                                'remark' => request('remark'),
                                'color' => $request->filled('color') ? request('color') : '#00BCD0FF',
                                'due_date' => $dueDate,
                                'debt_date' => $debtDate,
                                'updated_by' => $user->id,
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                        Debt::insert($data);
                        break;
                    case 'specific':
                        ScheduleSpecific::create([
                            'schedule_id' => $schedule->id,
                            'specific_date' => request('schedule.specific.date'),
                        ]);

                        Debt::create([
                            'schedule_id' => $schedule->id,
                            'user_id' => $user->id,
                            'type' => request('type'),
                            'item' => request('item'),
                            'amount' => request('amount'),
                            'remark' => request('remark'),
                            'color' => $request->filled('color') ? request('color') : '#00BCD0FF',
                            'due_date' => request('due_date.specific.date'),
                            'debt_date' => request('schedule.specific.date'),
                            'updated_by' => $user->id,
                        ]);
                        break;
                    default:break;
                }

                ScheduleEvent::create([
                    'schedule_id' => $schedule->id,
                    'type' => 'debt',
                    'content' => [
                        'item' => request('item'),
                        'amount' => request('amount'),
                        'remark' => request('remark'),
                    ],
                ]);

            });
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        return response()->json($response, 200);

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:debt,id',
            'item' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            Debt::where(['id' => request('id')])
                ->update([
                    'type' => request('type'),
                    'item' => request('item'),
                    'amount' => request('amount'),
                    'remark' => request('remark'),
                    'color' => $request->filled('color') ? request('color') : '#00BCD0FF',
                    'updated_by' => $user->id,
                ]);

            if ($request->filled(['paid'])) {
                Debt::where(['id' => request('id')])
                    ->update([
                        'type' => request('type'),
                        'paid' => request('paid'),
                        'paid_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $user->id,
                    ]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        $response['data'] = Debt::with(['users', 'schedule'])
            ->has('schedule')
            ->where(['id' => request('id')])
            ->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:debt,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            Debt::where(['id' => request('id')])->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        return response()->json($response, 200);
    }

    public function export(Request $request)
    {
        $query = Debt::with(['users', 'schedule'])
            ->has('schedule')
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['type']), function ($query) {
                return $query->where('type', request('type'));
            })
            ->when($request->filled(['from_date', 'to_date']), function ($query) {
                $from = date(request('from_date'));
                $to = date(request('to_date'));
                return $query->whereBetween('due_date', [$from, $to]);;
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('item', 'like', '%' . $keyword . '%');
                });
            });

        if ($request->filled(['sort_by', 'sort_desc'])) {
            $sortBys = request('sort_by');
            $sortDescs = request('sort_desc');
            $index = 0;
            foreach ($sortBys as $sortBy) {
                $sortDesc = $sortDescs[$index];
                if ($sortBy !== "action") {
                    $query->orderBy($sortBy, $sortDesc ? 'DESC' : 'ASC');
                }
                $index++;
            }
        }

        $result = $query->get();
        $headers = [
            __('User'),
            __('Item'),
            __('Amount'),
            __('Paid'),
            __('Debt Date'),
            __('Due Date'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            // [$keys, $values] = Arr::divide($item);
            // Log::debug($item);
            $row = [
                $item['users']['name'],
                $item['item'],
                Common::formatPrice($item['amount']),
                Common::formatPrice($item['paid']),
                $item['debt_date'],
                $item['due_date'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('debt', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

}