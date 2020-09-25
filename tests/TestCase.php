<?php

namespace Tests;

use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function authManager()
    {
        if (auth()->guard('visitor')->user()) {
            auth()->guard('visitor')->logout();
        }
        auth()->guard('api')->setUser(User::permission('manager')->latest()->firstOrFail());
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser(auth()->guard('api')->user())];
    }

    protected function authSupport()
    {
        if (auth()->guard('visitor')->user()) {
            auth()->guard('visitor')->logout();
        }
        auth()->guard('api')->setUser(User::permission('support')->latest()->firstOrFail());
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser(auth()->guard('api')->user())];
    }

    protected function authVisitor()
    {
        if (auth()->guard('api')->user()) {
            auth()->guard('api')->logout();
        }
        auth()->guard('visitor')->setUser(Visitor::latest()->firstOrFail());
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser(auth()->guard('visitor')->user())];
    }
}
