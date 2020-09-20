<?php

namespace App\Models;

use App\Models\Traits\HasEnterprise;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property string $id
 * @property int $enterprise_id
 * @property int $plan_id
 * @property int $user_id
 * @property string $period 期限, monthly annually biennially triennially
 * @property int|null $coupon_id 优惠券
 * @property string $price 价格
 * @property string $need_pay_price 实际应支付价格
 * @property string $paid_price 实际已支付价格
 * @property int $status 订单状态 0待支付 1已支付 -1已取消
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Enterprise $enterprise
 * @property-read \App\Models\stirng $public_id
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCoupon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereEnterpriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNeedPayPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePaidPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    use HasFactory, HasPublicId, HasEnterprise;

    protected $fillable = [
        'period',
        'coupon',
        'price',
        'need_pay_price',
        'paid_price',
        'status',
    ];

    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELLED = -1;

    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_ANNUALLY = 'annually';
    const PERIOD_BIENNIALLY = 'biennially';
    const PERIOD_TRIENNIALLY = 'triennially';

    const PERIOD_MAP = [
        self::PERIOD_MONTHLY => '月付',
        self::PERIOD_ANNUALLY => '年付',
        self::PERIOD_BIENNIALLY => '两年付',
        self::PERIOD_TRIENNIALLY => '三年付',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User|\Illuminate\Database\Query\Builder
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 套餐
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Plan|\Illuminate\Database\Query\Builder
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}
