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
        auth()->guard('api')->setUser(User::permission('manager')->latest()->firstOrFail());
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser(auth()->guard('api')->user())];
    }

    protected function authSupport()
    {
        auth()->guard('api')->setUser(User::permission('support')->latest()->firstOrFail());
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser(auth()->guard('api')->user())];
    }

    protected function authVisitor()
    {
        auth()->guard('visitor')->setUser(Visitor::latest()->firstOrFail());
        return ['Authorization' => 'Bearer ' . JWTAuth::fromUser(auth()->guard('visitor')->user())];
    }
}
