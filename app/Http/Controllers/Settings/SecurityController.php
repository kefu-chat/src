<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{
    /**
     * 各种绑定
     *
     * @param string $type
     * @return \Illuminate\Http\Response
     */
    public function bind(string $type)
    {
        Validator::make([
            'type' => $type,
        ], [
            'type' => ['required', 'string', 'in:wechat'],
        ]);

        switch ($type) {
            case 'wechat':
                /**
                 * @var \EasyWeChat\MiniProgram\Application $app
                 */
                $app = app('wechat.mini_program');
                return response()->success([
                    'qr' => base64_encode($app->app_code->getUnlimit('bind-' . $this->user->id, [
                        //'page' => '/pages/bind/qr',
                        'is_hyaline' => true,
                    ])),
                ]);
                break;

            default:
                # code...
                break;
        }
    }
}
