<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class FileController extends Controller
{
    //

    public function image($path = null)
    {

        $path = '/CSWallet-php-dev/storage/app';
        // dd($path);
        // dd(File::exists($path));
        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);
        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        if (Auth::user()) {
            return $response;
        } else if (Str::contains(request()->path(), ['icon', 'public'])) {
            return $response;
        } else {
            abort(404);
        }

    }

}