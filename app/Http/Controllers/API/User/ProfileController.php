<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\ProfileResource;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    //
    public function get(Request $request)
    {
        $user = Auth::user();
        $response = config('response.common.success');
        $resource = new ProfileResource($user);
        $response['data'] = $resource;
        return response()->json($response, 200);
    }

    public function update(Request $request)
    {
        try {
            Users::where(['id' => request('id')])
                ->update([
                    'email' => request('email'),
                    'phone' => request('phone'),
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error($e->getMessage());
            // $errorInfo = $e->errorInfo;
            $response = config('response.common.fail.database');
            $response['msg'] = $e->getMessage();
            return response()->json($response, 500);
        }

        $user = Users::where(['id' => request('id')])
            ->first();
        $resource = new ProfileResource($user);

        $response = config('response.common.success');
        $response['data'] = $resource;

        return response()->json($response, 200);
    }

}