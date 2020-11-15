<?php

namespace App\Exceptions;

use App\Models\UserSocialite;
use Illuminate\Validation\ValidationException;

class VerifyEmailException extends ValidationException
{
    /**
     * @param  \App\Models\User $user
     * @return static
     */
    public static function forUser($user)
    {
        $account = $user->userSocialites->where('type', UserSocialite::TYPE_EMAIL)->first();
        if (!$account) {
            // TODO: 没有 email?
        }
        return static::withMessages([
            'email' => [__('You must :linkOpen verify :linkClose your email first.', [
                'linkOpen' => '<a href="/email/resend?email='.urlencode($account->account).'">',
                'linkClose' => '</a>',
            ])],
        ]);
    }
}
