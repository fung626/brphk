<?php

namespace App\Http\Middleware;

use App\Models\User\Setting as UserSetting;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // $user = Auth::user();
        // Log::debug($user->region);
        $lang = 'tc';
        $user = Auth::user() ? Auth::user() : null;
        switch (request('lang')) {
            case 'en':
                $lang = 'en';
                break;
            case 'tc':
                $lang = 'tc';
                break;
            default:
                $lang = 'tc';
                break;
        }
        try {
            // Log::debug($lang);
            if ($user) {
                $setting = UserSetting::where(['user_id' => $user->id])->first();
                if ($setting === null) {
                    $items['lang'] = $lang;
                    UserSetting::updateOrCreate([
                        'user_id' => $user->id,
                    ], [
                        'items' => $items,
                    ]);
                } else {
                    $items = $setting->items;
                    $items['lang'] = $lang;
                    UserSetting::where(['user_id' => $user->id])
                        ->update(['items' => json_encode($items)]);
                }
            }
            App::setLocale($lang);
            // dd($lang);
        } catch (\Exception $e) {
            // Log::error($e->getMessage());
        }
        return $next($request);
    }
}