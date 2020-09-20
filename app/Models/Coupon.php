<?php

namespace App\Models;

use App\Models\Traits\HasEnterprise;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

/**
 * App\Models\Coupon
 *
 * @property string $id
 * @property string $name
 * @property int|null $enterprise_id 适用企业,为 null 表示全部
 * @property int|null $plan_id 适用产品,为 null 表示全部
 * @property string|null $periods 适用期限, casts array, 为空表示全部
 * @property int $using_limit 适用次数限制
 * @property int $type 类型 1打折 2抵扣
 * @property string $amount 抵扣金额, 打折比例
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Enterprise|null $enterprise
 * @property-read \App\Models\stirng $public_id
 * @property-read \App\Models\Plan|null $plan
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereEnterpriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon wherePeriods($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coupon whereUsingLimit($value)
 * @mixin \Eloquent
 */
class Coupon extends Model
{
    use HasFactory, HasPublicId, HasEnterprise;

    const TYPE_DECR = 1;
    const TYPE_DISC = 2;

    const TYPE_MAP = [
        self::TYPE_DECR => '折扣',
        self::TYPE_DISC => '抵扣',
    ];

    protected $fillable = [
        'name',
        'periods',
        'using_limit',
        'type',
        'amount',
    ];

    protected $casts = [
        'periods' => 'array',
    ];

    /**
     * 套餐
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Plan|\Illuminate\Database\Query\Builder
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * 企业
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Enterprise|\Illuminate\Database\Query\Builder
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    /**
     * 应用到订单
     *
     * @param Order $order
     * @return Order
     */
    public function usingOnOrder(Order $order)
    {
        if ($this->enterprise_id && $this->enterprise_id != $order->enterprise_id) {
            throw ValidationException::withMessages([
                'coupon' => '此优惠券无法给当前企业使用',
            ]);
        }
        if ($this->plan_id && $this->plan_id != $order->plan_id) {
            throw ValidationException::withMessages([
                'coupon' => '此优惠券无法购买此产品',
            ]);
        }
        if ($this->periods && collect($this->periods)->count() && !collect($this->periods)->contains($order->period)) {
            throw ValidationException::withMessages([
                'coupon' => '此优惠券不支持在此期限(' . Arr::get(Order::PERIOD_MAP, $order->period) . ')下使用',
                'period' => '此优惠券不支持在此期限(' . Arr::get(Order::PERIOD_MAP, $order->period) . ')下使用',
            ]);
        }
        if ($this->using_limit < 1) {
            throw ValidationException::withMessages([
                'coupon' => '此优惠券使用次数超限',
            ]);
        }

        switch ($this->type) {
            case self::TYPE_DISC:
                $need_pay_price = bcmul($order->price, $this->amount, 2);
                $order->fill([
                    'need_pay_price' => $need_pay_price,
                ]);
                break;

            case self::TYPE_DECR:
                $need_pay_price = bcsub($order->price, $this->amount, 2);
                $order->fill([
                    'need_pay_price' => $need_pay_price,
                ]);
                break;

            default:
                throw ValidationException::withMessages([
                    'coupon' => '此优惠券信息错误![type=' . $this->type . ']',
                ]);
                break;
        }

        $this->fill([
            'using_limit' => $this->using_limit - 1,
        ]);
        return $order;
    }
}
