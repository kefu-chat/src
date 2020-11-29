<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserSocialite;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Overtrue\LaravelWeChat\Facade;

class MiniappLoginController extends LoginController
{
    protected $socialiteType = UserSocialite::TYPE_WXAPP;
    protected $rules = [];

    /**
     * {@inheritDoc}
     */
    protected function credentials(Request $request)
    {
        return [
            'wxapp' => $request->input('wxapp'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'wxapp' => 'required|string',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function sendLoginResponseExtra()
    {
        return [
            'is_online' => true,
        ];
    }

    /**
     * 小程序登录
     */
    public function loginViaMiniApp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        if (!app()->has('wechat.mini_program.auth')) {
            app()->singleton('wechat.mini_program.auth', function () {
                return Facade::miniProgram()->auth;
            });
        }

        /**
         * @var \EasyWeChat\MiniProgram\Auth\Client $client
         */
        $client = app('wechat.mini_program.auth');
        $login = $client->session($request->input('code'));
        $openid = data_get($login, 'openid', false);
        if (!$openid) {
            throw ValidationException::withMessages([
                'code' => $login['errmsg'],
            ]);
        }

        $request->merge(['wxapp' => $openid]);
        return $this->login($request);
    }
}
