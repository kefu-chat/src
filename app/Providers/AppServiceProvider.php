<?php

namespace App\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Overtrue\LaravelWeChat\Facade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningUnitTests()) {
            Schema::defaultStringLength(191);
        }

        Collection::macro('setTransformer', function ($class) {
            /**
             * @var Collection $collection
             */
            $collection = $this;
            return $collection->transform(function ($item) use ($class) {
                return (new $class)->transform($item);
            });
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app()->bindIf('wechat.mini_program.auth', function () {
            return Facade::miniProgram()->auth;
        });
    }
}
