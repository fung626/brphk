<?php // Code within app\Helpers\Helper.php

namespace App\Mylibs;

use Illuminate\Cache\RateLimiter;

class MyThrottlesLogins
{

    public $username;

    public function retriesLeft()
    {
        return $this->limiter()->retriesLeft(
            $this->throttleKey($credentials), $this->maxAttempts()
        );
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    public function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input($this->username())) . '|' . $request->ip();
    }

}