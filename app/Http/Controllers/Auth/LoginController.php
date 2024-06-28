<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Validator;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function post(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|max:255|email|exists:users,email',
            'password' => 'required',
        ]);

        $user = Users::where('email', request('email'))->first();
        $auth = [
            'email' => $user->email,
            'password' => request('password'),
            'role' => $user->role,
        ];

        if (Hash::check(request('password'), $user->password) && Auth::attempt($auth)) {
            // Auth::attempt($auth);
            // dd(Auth::attempt($auth));
            $scopes = [];
            if ($user->role === "admin") {
                $scopes = ['*'];
            } else if ($user->role === "employee") {
                $scopes = [
                    'view-rent',
                    'create-rent',
                    'update-rent',
                    'delete-rent',
                    'view-company',
                    'view-company-bank-account',
                    'create-company-bank-account',
                    'update-company-bank-account',
                    'delete-company-bank-account',
                    'view-expenses',
                    'create-expenses',
                    'update-expenses',
                    'delete-expenses',
                ];
            }
            $token = $user->createToken($user->id . '-' . now(), $scopes)->accessToken;
            $response = config('response.common.success');
            $data = $user->toArray();
            $data['token'] = $token;
            $response['data'] = $data;
            return response()->json($response, 200);
        }

        $response = config('response.common.fail.parameter');
        return response()->json($response, 400);
    }

}