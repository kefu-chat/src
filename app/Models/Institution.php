<?php

namespace App\Models;

use App\Models\Traits\HasEnterprise;
use App\Models\Traits\HasPublicId;

/**
 * App\Models\Institution 项目、网站
 *
 * @property int $id
 * @property string|null $name 公司名字
 * @property string|null $website 网站
 * @property string $terminate_manual
 * @property string $terminate_timeout
 * @property string $technical_name
 * @property string $technical_phone
 * @property string $billing_name
 * @property string $billing_phone
 * @property string $theme
 * @property integer $timeout
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Visitor[] $visitors
 * @property-read int|null $visitors_count
 * @property-read \App\Models\Enterprise $enterprise
 * @property-read \App\Models\stirng $public_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution wherePlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Institution whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereWebsite($value)
 * @mixin \Eloquent
 * @property string $greeting_message 打招呼文本
 * @property int $enterprise_id 企业 ID
 * @property string|null $noreply
 * @property string|null $noreply_timeout 超时时间
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read int|null $conversations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereBillingName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereBillingPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereEnterpriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereGreetingMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereNoreply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereNoreplyTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereTechnicalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereTechnicalPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereTerminateManual($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereTerminateTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereTheme($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Institution whereTimeout($value)
 */
class Institution extends AbstractModel
{
    const DEFAULT = [
        'terminate_manual' => '本次对话已结束, 若您需要我们继续服务您可以重新打开咨询',
        'terminate_timeout' => '900',
        'greeting_message' => '您好, 请问有什么可以帮您的?',
        'technical_name' => '技术联系人',
        'technical_phone' => '',
        'billing_name' => '财务联系人',
        'billing_phone' => '',
        'timeout' => '900',
        'theme' => 'default',
        'noreply' => '我们客服目前无法回复, 请留下您的 QQ、微信、E-mail 或者 手机联系方式, 我们会第一时间给您回复',
        'noreply_timeout' => '60',
    ];

    use HasPublicId, HasEnterprise;

    protected $fillable = [
        'name',
        'website',
        'terminate_manual',
        'terminate_timeout',
        'greeting_message',
        'technical_name',
        'technical_phone',
        'billing_name',
        'billing_phone',
        'theme',
        'timeout',
        'noreply',
        'noreply_timeout',
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

    /**
     * 企业
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Enterprise|\Illuminate\Database\Query\Builder
     */
    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }

    /**
     * 会话
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Conversation|\Illuminate\Database\Query\Builder
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }
}
