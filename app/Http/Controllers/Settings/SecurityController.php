<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialite;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class SecurityController extends Controller
{
    /**
     * 用户ID签名
     */
    protected function sign($user_id)
    {
        if (config('app.debug')) {
            return $user_id;
        } else {
            return crc32(md5(config('app.key') . $user_id));
        }
    }

    /**
     * 各种绑定
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function bindQr(string $type)
    {
        Validator::make([
            'type' => $type,
        ], [
            'type' => ['required', 'string', 'in:wechat'],
        ]);

        $response = null;
        switch ($type) {
            case 'wechat':
                // /**
                //  * @var \EasyWeChat\MiniProgram\Application $app
                //  */
                // $app = app('wechat.mini_program');
                // $response = [
                //     'qr' => base64_encode($app->app_code->getUnlimit('bind-' . $this->user->id . '-' . crc32(md5(config('app.key') . $this->user->id)), [
                //         //'page' => '/pages/bind/qr',
                //         'is_hyaline' => true,
                //     ])),
                // ];
                $response = [
                    'qr' => config('kefu.qr_url') . '/pages/common/scan/scan?user=' . $this->user->id . '&sign=' . $this->sign($this->user->id),
                ];
                break;

            default:
                # code...
                break;
        }

        return response()->success($response);
    }

    /**
     * 各种绑定准备
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function bindPrepare(string $type, Request $request)
    {
        Validator::make([
            'type' => $type,
            'user' => $request->input('user'),
            'sign' => $request->input('sign'),
        ], [
            'type' => ['required', 'string', 'in:wechat'],
            'user' => ['required', 'integer'],
            'sign' => ['required', 'string'],
        ]);

        $response = null;
        switch ($type) {
            case 'wechat':
                if ($this->sign($request->input('user')) != $request->input('sign')) {
                    throw ValidationException::withMessages([
                        'sign' => '无效 sign',
                    ]);
                }

                try {
                    User::findOrFail($request->input('user'));
                } catch (ModelNotFoundException $e) {
                    throw ValidationException::withMessages([
                        'user' => '无效 user',
                    ]);
                }

                break;

            default:
                # code...
                break;
        }

        return response()->success($response);
    }

    /**
     * 各种绑定确认
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function bindConfirm(string $type, Request $request)
    {
        Validator::make([
            'type' => $type,
            'code' => $request->input('code'),
            'user' => $request->input('user'),
            'sign' => $request->input('sign'),
        ], [
            'type' => ['required', 'string', 'in:wechat'],
            'code' => ['required', 'string',],
            'user' => ['required', 'integer'],
            'sign' => ['required', 'string'],
        ]);

        $response = null;
        switch ($type) {
            case 'wechat':
                if ($this->sign($request->input('user')) != $request->input('sign')) {
                    throw ValidationException::withMessages([
                        'sign' => '无效 sign',
                    ]);
                }

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
                try {
                    // 一个OPENID只允许绑定一次
                    UserSocialite::where([
                        'type' => UserSocialite::TYPE_WXAPP,
                        'account' => $openid,
                    ])->delete();

                    /**
                     * @var User $user
                     */
                    $user = User::findOrFail($request->input('user'));
                    $socialite = new UserSocialite([
                        'account' => $openid,
                        'type' => UserSocialite::TYPE_WXAPP,
                        'verified_at' => now(),
                    ]);
                    $socialite->user()->associate($user);
                    $socialite->save();
                } catch (ModelNotFoundException $e) {
                    throw ValidationException::withMessages([
                        'user' => '无效 user',
                    ]);
                }

                break;

            default:
                # code...
                break;
        }

        return response()->success($response);
    }
}
