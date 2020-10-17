<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * 推送设备
 *
 * @property-read User|Visitor $user
 * @property int $id
 * @property string $user_type 用户表类型
 * @property int $user_id 用户ID
 * @property string|null $user_agent 设备类型: 操作系统+浏览器+版本
 * @property string|null $ip 设备IP地址
 * @property string $fingerprint 指纹
 * @property \App\Interfaces\PushSubscription $subscription 订阅详情，包含endpoint expirationTime keys.auth keys.p256dh
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice query()
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PushDevice whereUserType($value)
 * @mixin \Eloquent
 */
class PushDevice extends AbstractModel
{
    use HasFactory;

    protected $fillable = [
        'user_type',
        'user_id',
        'user_agent',
        'ip',
        'fingerprint',
        'subscription',
    ];

    protected $casts = [
        'subscription' => 'object',
    ];

    /**
     * 用户
     *
     * @return MorphTo|User|Visitor
     */
    public function user()
    {
        return $this->morphTo();
    }
}
