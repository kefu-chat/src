<?php

namespace App\Http\Controllers\Traits;

use App\Models\User;
use App\Models\UserSocialite;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * 微信小程序登录
 *
 * @property string $socialiteType
 * @method \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse login(Request $request)
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
                'code' => $login['errmsg'],
            ]);
        }

        $request->merge(['wxapp' => $openid]);
        $this->socialiteType = UserSocialite::TYPE_WXAPP;
        return $this->login($request);
    }
}
