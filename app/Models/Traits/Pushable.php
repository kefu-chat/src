<?php

namespace App\Models\Traits;

use App\Models\PushDevice;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * 可以 Webpush
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PushDevice[] $pushDevices
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany morphMany($related, $name, $type = null, $id = null, $localKey = null)
 */
trait Pushable
{
    /**
     * 推送设备们
     *
     * @return MorphMany|PushDevice
     */
    public function pushDevices()
    {
        return $this->morphMany(PushDevice::class, 'user', 'user_type', 'user_id');
    }
}
