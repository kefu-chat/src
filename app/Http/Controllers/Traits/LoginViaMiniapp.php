<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 微信小程序登录
 */
trait LoginViaMiniapp
{
    public function loginViaMiniApp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);


        /**
         * @var \EasyWeChat\MiniProgram\Application $app
         */
        $app = app('wechat.mini_program');
        $login = $app->auth->session($request->input('code'));
        $openid = data_get($login, 'openid', false);
        if (!$openid) {
            // TODO: error
            throw ValidationException::withMessages([
                'code' => 'code ' . $request->input('code') . ' 无法获取 openid ' . json_encode($login),
            ]);
        }
    }
}
