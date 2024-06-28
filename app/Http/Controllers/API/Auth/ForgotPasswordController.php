<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Auth\ForgotPassword;
use App\Models\Auth\PasswordReset as PasswordResetModel;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Validator;

class ForgotPasswordController extends Controller
{

    public function forgot(Request $request)
    {
        // Mail::send('mail.demo', ['name' => 'fung626'], function ($message) {
        //     $message->from('unstoppabletradingdeveloper@gmail.com', 'test');
        //     $message->to('fung626@gmail.com')->subject('test');
        // });
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        // dd(request('email'));

        $user = Users::where('email', request('email'))->first();

        if ($user) {
            $response = Password::sendResetLink(['email' => request('email')], function ($user, $token) {
                Mail::to($user->email)
                    ->send(new ForgotPassword($user, $token));
            });
            // dd($response);
            Log::debug($response);
            switch ($response) {
                case Password::RESET_LINK_SENT:
                    // dd($response);
                    $response = config('response.common.success');
                    return response()->json($response, 200);
                case Password::INVALID_USER:
                    $response = config('response.common.fail.unauthorised');
                    return response()->json($response, 400);
                case Password::RESET_THROTTLED:
                    $response = config('response.common.fail.throttled');
                    return response()->json($response, 400);
            }
        } else {
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }
    }

    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:users,id',
            'token' => 'required|string',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Users::where('id', request('id'))->first();
        $passwordReset = PasswordResetModel::where(['email' => $user->email])->first();

        if (!$passwordReset) {
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        if (!Hash::check(request('token'), $passwordReset->token)) {
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            PasswordResetModel::where(['email' => $user->email])->delete();
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        try {
            DB::transaction(function () use ($user) {
                Users::where([
                    'id' => $user->id,
                ])->update([
                    'password' => bcrypt(request('confirm_password')),
                ]);
                PasswordResetModel::where(['email' => $user->email])->delete();
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = config('response.common.fail.database');
            return response()->json($response, 400);
        }

        $response = config('response.common.success');
        return response()->json($response, 200);

    }

    public function find(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|string|exists:users,id',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            $response = config('response.common.fail.parameter');
            $response['data'] = $validator->errors();
            return response()->json($response, 400);
        }

        $user = Users::where('id', request('id'))->first();
        $passwordReset = PasswordResetModel::where(['email' => $user->email])->first();

        if (!$passwordReset) {
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        if (!Hash::check(request('token'), $passwordReset->token)) {
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            PasswordResetModel::where(['email' => $user->email])->delete();
            $response = config('response.common.fail.parameter');
            return response()->json($response, 400);
        }

        $response = config('response.common.success');
        return response()->json($response, 200);
    }

}