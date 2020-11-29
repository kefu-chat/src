<?php

namespace App\Http\Controllers\Auth;

use App\Models\UserSocialite;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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

        /**
         * @var \EasyWeChat\MiniProgram\Auth\Client $client
         */
        $client = app('wechat.mini_program.auth');
        $login = $client->session($request->input('code'));
        $openid = data_get($login, 'openid', false);
        if (!$openid) {
            return $this->sendFailedLoginResponse($request->merge(['errmsg' => $login['errmsg']]));
        }

        $request->merge(['wxapp' => $openid]);
        return $this->login($request);
    }

    /**
     * {@inheritDoc}
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => $request->input('errmsg', trans('auth.failed')),
            'code' => 401,
        ] + $this->sendLoginResponseExtra());
    }
}
