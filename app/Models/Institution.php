<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;

/**
 * App\Models\Institution
 *
 * @property int $id
 * @property string|null $name 公司名字
 * @property string|null $serial 公司注册号
 * @property int $plan_id 套餐 ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Visitor[] $visitors
 * @property-read int|null $visitors_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereUpdatedAt($value)
 */
class Institution extends AbstractModel
{
    use HasPublicId;

    protected $fillable = [
        'name',
        'serial',
    ];

    /**
     * 员工
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|User|\Illuminate\Database\Query\Builder
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * 访客
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|User|\Illuminate\Database\Query\Builder
     */
    public function visitors()
    {
        return $this->hasMany(Visitor::class);
    }
}
