<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    //

    public function get(Request $request)
    {
        $routes = Route::getRoutes();
        $response = config('response.common.success');
        $response['data'] = $routes;
        return response()->json($response, 200);
    }
}