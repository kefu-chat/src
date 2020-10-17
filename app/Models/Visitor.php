<?php

namespace App\Models;

use App\Models\Traits\HasInstitution;
use App\Models\Traits\HasPublicId;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthAuthenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\Visitor
 *
 * @property int $id
 * @property string $unique_id 唯一 ID
 * @property string $name 名字
 * @property string $email 电子邮件
 * @property string $phone 手机号
 * @property string|null $avatar 头像
 * @property string|null $memo 备注
 * @property int $institution_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read int|null $conversations_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereMemo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereUniqueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Visitor whereUpdatedAt($value)
 */
class Visitor extends AbstractModel implements JWTSubject, AuthAuthenticatable
{
    use HasInstitution, Authenticatable, HasPublicId, HasPushSubscriptions, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'unique_id',
        'name',
        'email',
        'phone',
        'avatar',
        'memo',
        'address',
    ];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * @return int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
