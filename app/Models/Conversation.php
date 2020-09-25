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
 * @property bool $online_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Torann\GeoIP\GeoIP|\Torann\GeoIP\Location $geo
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Visitor $visitor
 * @property-read Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation> $messages
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
 */
class Conversation extends AbstractModel
{
    use HasInstitution, HasPublicId;

    protected $fillable = [
        'ip',
        'url',
        'first_reply_at',
        'last_reply_at',
        'ended_at',
        'online_status',
        'title',
        'userAgent',
        'languages',
        'referer',
    ];

    protected $casts = [
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
