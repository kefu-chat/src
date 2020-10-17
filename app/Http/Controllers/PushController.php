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
        $this->user->pushDevices();
        $subscription = $request->input('subscription');
        ksort($subscription);
        $fingerprint = md5(json_encode($subscription));

        /**
         * @var PushDevice $device
         */
        $device = PushDevice::where('fingerprint', $fingerprint)->first();
        if (!$device) {
            $device = new PushDevice();
            $device->fill([
                'fingerprint' => $fingerprint,
                'subscription' => $subscription,
                'user_agent' => $request->header('HTTP_AGENT'),
                'ip' => $request->getClientIp(),
            ]);
        }
        if ($device->user_id != $this->user->id || $device->user_type != get_class($this->user)) {
            $device->user()->associate($this->user);
        }

        $device->save();

        return response()->success($device);
    }
}
