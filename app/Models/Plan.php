<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $name 套餐名字
 * @property string $price_monthly 月付价格
 * @property string $price_annually 年付价格
 * @property string $price_biennially 两年付价格
 * @property string $price_triennially 三年付价格
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
        'available',
    ];

    protected $casts = [
        'available' => 'bool',
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
