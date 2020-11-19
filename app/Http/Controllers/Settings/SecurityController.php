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
                /**
                 * @var \EasyWeChat\MiniProgram\Application $app
                 */
                $app = app('wechat.mini_program');
                $response = [
                    'qr' => base64_encode($app->app_code->getUnlimit('bind-' . $this->user->id . '-' . crc32(md5(config('app.key') . $this->user->id)), [
                        //'page' => '/pages/bind/qr',
                        'is_hyaline' => true,
                    ])),
                ];
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
            'scene' => $request->input('scene'),
            'code' => $request->input('code'),
        ], [
            'type' => ['required', 'string', 'in:wechat'],
            'scene' => ['required', 'string'],
            'code' => ['required', 'string', 'regex:bind\-{\d+}\-{\d+}'],
        ]);

        $response = null;
        switch ($type) {
            case 'wechat':
                /**
                 * @var \EasyWeChat\MiniProgram\Application $app
                 */
                $app = app('wechat.mini_program');
                try {
                    list($bind, $user_id, $crc32) = explode('-', $request->input('code'));
                    if ($crc32 !== crc32(md5(config('app.key') . $user_id))) {
                        throw new Exception('bad signature');
                    }
                } catch (Throwable $e) {
                    throw ValidationException::withMessages([
                        'scene' => '无效 scene',
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
                    /**
                     * @var User $user
                     */
                    $user = User::findOrFail($user_id);
                    $socialite = new UserSocialite([
                        'account' => $openid,
                        'type' => UserSocialite::TYPE_WXAPP,
                        'verified_at' => now(),
                    ]);
                    $socialite->user()->associate($user);
                    $socialite->save();
                } catch (ModelNotFoundException $e) {
                    throw ValidationException::withMessages([
                        'scene' => '无效 scene',
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
