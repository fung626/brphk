<?php

namespace App\Http\Controllers\API\Rent;

use App\Http\Controllers\Controller;
use App\Models\Rent;
use App\Mylibs\Rent as RentLib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArrearsController extends Controller
{
    //
    public function get(Request $request)
    {
        $user = Auth::user();
        $query = Rent::hasRentArrears()
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($user->role === "employee", function ($query) use ($user) {
                return $query->where('rent.user_id', $user->id);
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('owner', 'like', '%' . $keyword . '%')
                        ->orWhere('tenant', 'like', '%' . $keyword . '%')
                        ->orWhere('property', 'like', '%' . $keyword . '%')
                        ->orWhere('remark', 'like', '%' . $keyword . '%');
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
            $data = RentLib::withRentArrearsMonthProperties($result);
            $response['data'] = $data;
        } else {
            $result = $query->get();
            $data = RentLib::withRentArrearsMonthProperties($result);
            $response['data'] = $data;
        }

        return response()->json($response, 200);
    }

    public function export(Request $request)
    {
        $query = Rent::hasRentArrears()
            ->when($request->filled(['user_id']), function ($query) {
                return $query->where('user_id', request('user_id'));
            })
            ->when($request->filled(['search']), function ($query) {
                $keyword = trim(request('search'));
                return $query->where(function ($query) use ($keyword) {
                    $query->where('id', 'like', '%' . $keyword . '%')
                        ->orWhere('owner', 'like', '%' . $keyword . '%')
                        ->orWhere('tenant', 'like', '%' . $keyword . '%')
                        ->orWhere('property', 'like', '%' . $keyword . '%')
                        ->orWhere('remark', 'like', '%' . $keyword . '%');
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

        $rents = $query->get();
        $result = RentLib::withRentArrearsMonthProperties($rents);
        $data = $result['data'];
        $rows = [];
        foreach ($result['data'] as $item) {
            $row = [
                $item->user->name,
                $item->owner,
                $item->tenant,
                $item->property,
                Common::formatPrice($item->amount),
                $item->start_date,
                $item->fix_term_tenancy_date,
                $item->break_clause_date,
            ];
            foreach ($result['months'] as $month) {
                $row[] = isset($item->$month) ? $item->$month : false;
            }
            // dd($item, $row);
            $rows[] = $row;
        }

        $path = MyPhpOffice::exportTableWithPath('rent-payment', $rows, $result['headers'], 'pdf');

        return response()
            ->download($path)
            ->deleteFileAfterSend(true);
    }

}