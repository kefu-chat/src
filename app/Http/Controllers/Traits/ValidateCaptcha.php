<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

/**
 * 验证码校验
 */
trait ValidateCaptcha
{
    public function validateCaptcha($captcha_challenge, $captcha_answer)
    {
        list($answer, $tk, $expires_timestamp) = decrypt($captcha_challenge);
        if (strtolower($captcha_answer) != strtolower($answer)) {
            throw ValidationException::withMessages([
                'captcha_answer' => '验证码错误，请重试',
            ]);
        }

        if ($expires_timestamp < now()->getTimestamp() || Cache::decrement('captcha_' . $tk) <= 0) {
            Cache::forget('captcha_' . $tk);
            throw ValidationException::withMessages([
                'captcha_answer' => '验证码失效！请重新获取',
            ])->status(439);
        }
    }
}
