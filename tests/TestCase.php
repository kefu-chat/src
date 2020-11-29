<?php

namespace Tests;

use App\Models\User;
use App\Models\Visitor;
use EasyWeChat\Kernel\ServiceContainer;
use EasyWeChat\MiniProgram\Auth\AccessToken;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Create API Client mock object.
     *
     * @param string                                   $name
     * @param array|string                             $methods
     * @param \EasyWeChat\Kernel\ServiceContainer|null $app
     *
     * @return \Mockery\Mock
     */
    public function mockApiClient($name, $methods = [], ServiceContainer $app = null)
    {
        $methods = implode(',', array_merge([
            'httpGet', 'httpPost', 'httpPostJson', 'httpUpload',
            'request', 'requestRaw', 'requestArray', 'registerMiddlewares',
        ], (array) $methods));

        $client = \Mockery::mock(
            $name . "[{$methods}]",
            [
                $app ?? \Mockery::mock(ServiceContainer::class),
                \Mockery::mock(AccessToken::class),
            ]
        )->shouldAllowMockingProtectedMethods();
        $client->allows()->registerHttpMiddlewares()->andReturnNull();

        return $client;
    }

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
