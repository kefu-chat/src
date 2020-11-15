<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserSocialite
 *
 * @property int $id
 * @property int $user_id 用户 ID
 * @property string $type 登录方式
 * @property string $account 账号
 * @property \Illuminate\Support\Carbon|null $verified_at 验证时间
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserLogin whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSocialite whereVerifiedAt($value)
 * @mixin \Eloquent
 */
class UserSocialite extends Model
{
    const TYPE_EMAIL = 'email'; // 邮箱
    const TYPE_PHNONE = 'phnone'; // 手机
    const TYPE_WXAPP = 'wxapp'; // 微信小程序
    const TYPE_DINGTALK = 'dingtalk'; // 钉钉

    use HasFactory;

    protected $fillable = [
        'type',
        'account',
        'verified_at',
    ];

    protected $dates = [
        'verified_at',
    ];

    /**
     * 用户
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|\App\Models\User|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
