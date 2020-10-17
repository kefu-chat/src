<?php

namespace App\Http\Controllers;

use App\Models\PushDevice;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * 订阅
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'subscription' => ['required', 'array'],
            'subscription.endpoint' => ['required', 'url'],
            'subscription.keys' => ['required', 'array'],
            'subscription.keys.auth' => ['required', 'string'],
        ]);

        $this->user->updatePushSubscription(
            str_replace('https://fcm.googleapis.com', 'http://fcm.ssls.com.cn', $request->input('subscription.endpoint')),
            $request->input('subscription.keys.p256dh'),
            $request->input('subscription.keys.auth')
        );

        return response()->success($this->user->pushSubscriptions()->latest()->first());
    }
}
