<?php

namespace App\Notifications;

use App\Models\Traits\HasPublicId;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    const EXPIRES_TTL = 60 * 24;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  Authenticatable|HasPublicId $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $appUrl = config('kefu.client_url', config('app.url'));

        $url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(self::EXPIRES_TTL),
            ['user' => $notifiable->public_id]
        );

        return str_replace(url('/api'), $appUrl, $url);
    }
}
