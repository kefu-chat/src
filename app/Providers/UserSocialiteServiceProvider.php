<?php

namespace App\Providers;

use App\Auth\SocialiteUserProvider;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class UserSocialiteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * @var \Illuminate\Auth\AuthManager $auth
         */
        $auth = auth();
        $auth->provider('socialite', function (Container $app, $config) {
            return new SocialiteUserProvider($app['hash'], $config['model']);
        });
    }
}
