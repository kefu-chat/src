<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $name 套餐名字
 * @property string $price_monthly 月付价格
 * @property string $price_annually 年付价格
 * @property string $price_biennially 两年付价格
 * @property string $price_triennially 三年付价格
 * @property int $concurrent 同时对话数
 * @property int $sites 站点数
 * @property int $seats 坐席数
 * @property int $archive_days 对话存档时间
 * @property bool $statistics 统计报表
 * @property int $theme 主题
 * @property int $inivite 每天可邀请次数
 * @property bool $remove_powered_by 可移除版权信息
 * @property bool $support_wechat 1对1 微信QQ支持
 * @property bool $support_phone 1对1 电话支持
 * @property bool $desensitize 用户资料脱敏
 * @property bool $sso 对接企业统一登录
 * @property bool $private_deploy 私有部署
 * @property bool $available 是否开放购买
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Enterprise[] $enterprises
 * @property-read int|null $enterprises_count
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePriceAnnually($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePriceBiennially($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePriceMonthly($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan wherePriceTriennially($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Plan whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Plan extends Model
{
    use HasFactory, HasPublicId;

    protected $fillable = [
        'name',
        'price_monthly',
        'price_annually',
        'price_biennially',
        'price_triennially',
        'concurrent', //同时对话数
        'seats', //坐席数
        'sites', //站点数
        'statistics', //统计报表
        'theme', //主题
        'archive_days',
        'remove_powered_by',
        'invite',
        'support_wechat', //1对1 微信QQ支持
        'support_phone', //1对1 电话支持
        'desensitize', //用户资料脱敏
        'sso', //对接企业统一登录
        'private_deploy',
        'available',
    ];

    protected $casts = [
        'available' => 'bool',
        'statistics' => 'bool',
        'support_wechat' => 'bool',
        'support_phone' => 'bool',
        'desensitize' => 'bool',
        'sso' => 'bool',
        'theme' => 'integer',
        'archive_days' => 'integer',
        'remove_powered_by' => 'boolean',
        'private_deploy' => 'bool',
        'available' => 'bool',
        'price_monthly' => 'float',
        'price_annually' => 'float',
        'price_biennially' => 'float',
        'price_triennially' => 'float',
    ];

    /**
     * 套餐下面的企业们
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Enterprise|\Illuminate\Database\Query\Builder
     */
    public function enterprises()
    {
        return $this->hasMany(Enterprise::class);
    }
}
