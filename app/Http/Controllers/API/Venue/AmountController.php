<?php

namespace App\Http\Controllers\API\Venue;

use App\Http\Controllers\Controller;
use App\Http\Resources\Venue\AmountCollection;
use App\Models\Venue\IEAmount;
use App\Models\Venue\IEItem;
use App\Mylibs\Common;
use App\Mylibs\MyPhpOffice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;

class AmountController extends Controller
{
    //

    public function get(Request $request)
    {

        $query = IEAmount::with(['user', 'venue', 'item'])
            ->when($request->filled(['venue_id']), function ($query) {
                return $query->where('venue_id', request('venue_id'));
            })
            ->when($request->filled(['venue_item_id']), function ($query) {
                return $query->where('venue_ie_item_id', request('venue_item_id'));
            })
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('amount', 'like', '%' . $keyword . '%');
                })->orWhereHas('venue', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                })->orWhereHas('item', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
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

        $response = config('response.common.success');
        if ($request->filled(['per_page', 'page'])) {
            $result = $query->paginate(request('per_page'), ['*'], 'page', request('page'));
            $resource = new AmountCollection($result);
            $response['data'] = [
                'data' => $resource,
                'total' => $result->total(),
                'last_page' => $result->lastPage(),
                'has_more_pages' => $result->hasMorePages(),
            ];
        } else {
            $result = $query->get();
            $resource = new AmountCollection($result);
            $response['data'] = $resource;
        }

        return response()->json($response, 200);
    }

    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'venue_item_id' => 'required_without:venue_item|string|exists:venue_ie_item,id',
            'venue_item.id' => 'required_without:venue_item_id|string|exists:venue_ie_item,id',
            'type' => 'required|in:income,expenditure',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date_format:Y-m-d',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();
        // request('owner')

        try {
            // $_item = request('venue_item');
            $item = IEItem::where([
                'id' => request('venue_item_id'),
            ])->orWhere([
                'id' => request('venue_item.id'),
            ])->first();

            IEAmount::create([
                'venue_id' => $item->venue_id,
                'venue_ie_item_id' => $item->id,
                'user_id' => $user->id,
                'type' => request('type'),
                'amount' => request('amount'),
                'date' => request('date'),
            ]);
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
            'id' => 'required|string|exists:venue_ie_amount,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Auth::user();

        try {
            IEAmount::where(['id' => request('id')])
                ->update([
                    'name' => request('name'),
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $response = config('response.common.success');
        $response['data'] = IEAmount::with(['user', 'venue', 'item'])->where(['id' => request('id')])->first();
        return response()->json($response, 200);
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:venue_ie_amount,id',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        try {
            IEAmount::where(['id' => request('id')])->delete();
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
        $query = IEAmount::with(['user', 'venue', 'item'])
            ->when($request->filled(['venue_id']), function ($query) {
                return $query->where('venue_id', request('venue_id'));
            })
            ->when($request->filled(['venue_item_id']), function ($query) {
                return $query->where('venue_ie_item_id', request('venue_item_id'));
            })
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('amount', 'like', '%' . $keyword . '%');
                })->orWhereHas('venue', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
                })->orWhereHas('item', function ($query) use ($keyword) {
                    $query->where('name', 'like', '%' . $keyword . '%');
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
            __('Venue'),
            __('Item'),
            __('Type'),
            __('Amount'),
            __('Date'),
            __('Created at'),
            __('Updated at'),
        ];
        $rows = [];
        foreach ($result->toArray() as $item) {
            $row = [
                $item['user']['name'],
                $item['venue']['name'],
                $item['item']['name'],
                __($item['type']),
                Common::formatPrice($item['amount']),
                $item['date'],
                Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
            ];
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('venue-item-amount', $rows, $headers, 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

}