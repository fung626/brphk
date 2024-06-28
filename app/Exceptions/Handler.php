<?php

namespace App\Exceptions;

// use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // dd($e);
            //
        });
    }

    // /**
    //  * Report or log an exception.
    //  *
    //  * @param  \Exception  $exception
    //  * @return void
    //  */
    // public function report(Exception $exception)
    // {
    //     if ($this->shouldReport($exception)) {
    //         Log::critical($exception);
    //     }
    //     parent::report($exception);
    // }

    // /**
    //  * Render an exception into an HTTP response.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  \Exception  $exception
    //  * @return \Illuminate\Http\Response
    //  */
    // public function render($request, Exception $exception)
    // {
    //     // $request->request->add(['ip' => Common::userIP()]);
    //     if (request()->is('api/*')) {
    //         dd('YES');
    //         if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
    //             // return parent::render($request, $exception);
    //             return response(config('response.common.fail.credentials'), 401);
    //         }
    //         if ($exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
    //             return response()->json(config('response.common.fail.data'), 400);
    //         }
    //         if ($exception instanceof \Illuminate\Http\Exceptions\ThrottleRequestsException) {
    //             return response()->json([
    //                 'error' => true,
    //                 'code' => 429,
    //                 'msg' => 'Too Many Attempts.',
    //             ], 429);
    //         }
    //     }

    //     return parent::render($request, $exception);
    // }
}