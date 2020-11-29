<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserSocialite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MiniappLoginController extends LoginController
{
    protected $socialiteType = UserSocialite::TYPE_WXAPP;
    protected $rules = [];

    /**
     * 小程序登录
     */
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
            throw ValidationException::withMessages([
                'code' => $login['errmsg'],
            ]);
        }

        $request->merge(['wxapp' => $openid]);
        return $this->login($request);
    }
}
