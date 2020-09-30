<?php

namespace App\Models;

use App\Models\Traits\HasInstitution;
use App\Models\Traits\HasPublicId;

/**
 * App\Models\Conversation
 *
 * @property int $id
 * @property int $visitor_id 访客 ID
 * @property int $user_id 分配给的客服 ID
 * @property int $institution_id 企业 ID
 * @property string $ip 访客 IP
 * @property string|null $url 从那个页面来
 * @property string|null $first_reply_at 初次回复时间
 * @property string|null $last_reply_at 上次回复时间
 * @property bool $status 状态 1开启 0 关闭
 * @property bool $online_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Torann\GeoIP\GeoIP|\Torann\GeoIP\Location $geo
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Visitor $visitor
 * @property-read Message[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Message> $messages
 * @property-read Message[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Message> $userMessages
 * @property-read Message[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Message> $visitorMessages
 * @property-read Message $lastMessage
 * @property-read string $color
 * @property-read string $icon
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereFirstReplyAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereLastReplyAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Conversation whereVisitorId($value)
 * @property string|null $title 页面抬头
 * @property string|null $userAgent 浏览器
 * @property array|null $languages 语言
 * @property string|null $referer 从那个页面来
 * @property \Illuminate\Support\Carbon|null $ended_at 会话结束时间
 * @property-read \App\Models\stirng $public_id
 * @property-read \App\Models\Institution $institution
 * @property-read int|null $messages_count
 * @property-read int|null $user_messages_count
 * @property-read int|null $visitor_messages_count
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereLanguages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereOnlineStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereReferer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Conversation whereStatus($value)
 * @mixin \Eloquent
 */
class Conversation extends AbstractModel
{
    use HasInstitution, HasPublicId;

    const STATUS_CLOSED = false;
    const STATUS_OPEN = true;

    protected $fillable = [
        'ip',
        'url',
        'first_reply_at',
        'last_reply_at',
        'ended_at',
        'status',
        'online_status',
        'title',
        'userAgent',
        'languages',
        'referer',
    ];

    protected $casts = [
        'status' => 'bool',
        'online_status' => 'bool',
        'languages' => 'array',
    ];

    protected $dates = [
        'first_reply_at',
        'last_reply_at',
        'ended_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Visitor|\Illuminate\Database\Query\Builder
     */
    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User|\Illuminate\Database\Query\Builder
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Message|\Illuminate\Database\Query\Builder
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Message|\Illuminate\Database\Query\Builder
     */
    public function visitorMessages()
    {
        return $this->hasMany(Message::class)->where('sender_type', Visitor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Message|\Illuminate\Database\Query\Builder
     */
    public function userMessages()
    {
        return $this->hasMany(Message::class)->where('sender_type', User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Message|\Illuminate\Database\Query\Builder
     */
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest('id');
    }

    public function getColorAttribute()
    {
        $colors = [
            "#eb2f96",
            "#52c41a",
            "#faad14",
            "#0fc198",
            "#896ee6",
        ];
        return $colors[$this->getOriginal('id') % count($colors)];
    }

    public function getIconAttribute()
    {
        return '/assets/tmp/img/random/' . ($this->getOriginal('id') % 50) . '.svg';
    }

    public function getGeoAttribute()
    {
        return geoip($this->ip)->toArray();
    }

    public function offline(Visitor $visitor)
    {
        if ($visitor->id != $this->visitor_id) {
            abort(404);
        }
        $this->fill(['online_status' => false,]);
        $this->save();
        return $this;
    }

    public function online(Visitor $visitor)
    {
        if ($visitor->id != $this->visitor_id) {
            abort(404);
        }
        $this->fill(['online_status' => true,]);
        $this->save();
        return $this;
    }
}
