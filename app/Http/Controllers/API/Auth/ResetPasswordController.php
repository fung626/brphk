<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Validator;

class ResetPasswordController extends Controller
{
    //
    public function post(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');

            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        // $user = Auth::user();
        // $id = Auth::user()->id;
        $user = Users::where(['id' => Auth::user()->id])->first();
        // Log::debug(Hash::make(request('new_password')));

        if (!Hash::check(request('old_password'), $user->password)) {
            $response = config('response.common.fail.parameter');
            $response['msg'] = "Incorrent old password. Please try again!!";
            return response()->json($response, 400);
        }

        try {
            // Users::where(['id' => $user->id])
            //     ->update([
            //         'password' => Hash::make(reuqest('new_password')),
            //     ]);
            $user->password = Hash::make(request('new_password'));
            $user->save();
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
}
