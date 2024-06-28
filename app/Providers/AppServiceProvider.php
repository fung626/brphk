<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Validator::extend('before_weekday', function ($attribute, $value, $parameters, $validator) {
            $f = date('N', strtotime($value));
            foreach ($parameters as $parameter) {
                $t = date('N', strtotime($parameter));
                if ($t < $f) {
                    $validator->addReplacer('before_weekday', function ($message, $attribute, $rule, $parameters) use ($parameter) {
                        return \str_replace(':weekday', $parameter, $message);
                    });
                    return false;
                }
            }
            return true;
        });
        Validator::extend('after_weekday', function ($attribute, $value, $parameters, $validator) {
            $f = date('N', strtotime($value));
            foreach ($parameters as $parameter) {
                $t = date('N', strtotime($parameter));
                if ($t > $f) {
                    $validator->addReplacer('after_weekday', function ($message, $attribute, $rule, $parameters) use ($parameter) {
                        return \str_replace(':weekday', $parameter, $message);
                    });
                    return false;
                }
            }
            return true;
        });
    }
}