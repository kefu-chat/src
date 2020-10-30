<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use NikolaiT\Captcha\SVGCaptcha;

class CaptchaController extends Controller
{
    /**
     * 订阅
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function svg(Request $request)
    {
        $tk = Str::random();
        $times = 5;
        $expires = now()->addMinute(10);
        $captcha = SVGCaptcha::getInstance(4, 100, 40, SVGCaptcha::EASY);
        list($answer, $svg) = $captcha->getSVGCaptcha();

        Cache::put('captcha_' . $tk, $times, $expires->diffInSeconds(now()));

        return response()->success([
            'captcha_image' => str_replace([PHP_EOL, '  '], ' ', preg_replace('/[ ]+/', ' ', $svg)),
            'captcha_challenge' => encrypt([$answer, $tk, $expires->getTimestamp()]),
        ]);
    }
}
