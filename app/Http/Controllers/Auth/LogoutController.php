<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\Log;

class LogoutController extends Controller
{
    //

    public function post(Request $request)
    {
        // Log::debug(Auth::user());
        $data = Auth::user();
        Auth::logout();
        $response = config('response.common.success');
        $response['data'] = $data;
        return response()->json($response, 200);
    }
}