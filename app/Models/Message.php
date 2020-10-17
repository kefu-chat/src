<?php

namespace App\Models;

use App\Models\Traits\HasInstitution;
use App\Models\Traits\HasPublicId;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $conversation_id 会话 ID
 * @property int $type 1文字 2图片
 * @property string $content 内容
 * @property null|string[] $options 可选项 用于机器人规则
 * @property string $sender_type
 * @property int $sender_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Conversation $conversation
 * @property-read string $sender_type_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereConversationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereVisitorId($value)
 * @property int $institution_id 组织 ID
 * @property-read \App\Models\stirng $public_id
 * @property-read \App\Models\Institution $institution
 * @property-read User|Visitor $sender
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSenderType($value)
 * @mixin \Eloquent
 */
class Message extends AbstractModel
{
    use HasInstitution, HasPublicId;

    const TYPE_TEXT = 1;
    const TYPE_IMAGE = 2;
    const TYPE_MAP = [
        self::TYPE_TEXT => '文本消息',
        self::TYPE_IMAGE => '图片消息',
    ];
    const SENDER_TYPE_MAP = [
        User::class => 'user',
        Visitor::class => 'visitor',
    ];

    protected $fillable = [
        'type',
        'content',
        'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }

    public function getSenderTypeTextAttribute()
    {
        return data_get(self::SENDER_TYPE_MAP, $this->sender_type, $this->sender_type);
    }
}
