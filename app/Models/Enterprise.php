<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Enterprise
 *
 * @property int $id
 * @property string|null $name 企业名称
 * @property string|null $serial 公司注册号
 * @property int $plan_id 套餐 ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Institution[] $institutions
 * @property-read \App\Models\Plan $plan
 * @property-read int|null $institutions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise query()
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Enterprise whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial',
    ];

    /**
     * 企业下面的网站们
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Institution|\Illuminate\Database\Query\Builder
     */
    public function institutions()
    {
        return $this->hasMany(Institution::class);
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
