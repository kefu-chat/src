<?php

namespace App\Models;

use App\Models\Traits\HasEnterprise;
use App\Models\Traits\HasInstitution;
use App\Models\Traits\HasPublicId;
use App\Models\Traits\SetTransformer;
use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Xiaohuilam\Laravel\WxappNotificationChannel\Traits\UserTrait as WxappNotification;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $enterprise_id 企业 ID
 * @property int $institution_id 网站 ID
 * @property string $name
 * @property string $avatar 头像
 * @property string|null $title 职位
 * @property string|null $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Institution $institution
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \App\Models\Enterprise $enterprise
 * @property-read \App\Models\stirng $public_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Conversation[] $conversations
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OAuthProvider[] $oauthProviders
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read \Illuminate\Database\Eloquent\Collection|\NotificationChannels\WebPush\PushSubscription[] $pushSubscriptions
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\UserSocialite[] $userSocialites
 * @property-read int|null $conversations_count
 * @property-read int|null $oauth_providers_count
 * @property-read int|null $permissions_count
 * @property-read int|null $push_subscriptions_count
 * @property-read int|null $roles_count
 * @property-read int|null $user_socialites_count
 * @property-read int|null $notifications_count
 * @property-read string $openid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereInstitutionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEnterpriseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable, SetTransformer, HasInstitution, HasEnterprise, HasFactory, HasRoles, HasPublicId, SoftDeletes, HasPushSubscriptions, WxappNotification;

    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'avatar', 'title',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the oauth providers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function oauthProviders()
    {
        return $this->hasMany(OAuthProvider::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
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

    /**
     * 会话清单
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Conversation|\Illuminate\Database\Query\Builder
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * 用户的登录方式们
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|UserSocialite|\Illuminate\Database\Query\Builder
     */
    public function userSocialites()
    {
        return $this->hasMany(UserSocialite::class);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        $socialite = $this->userSocialites->where('type', UserSocialite::TYPE_EMAIL)->first();
        $socialite->fill(['verified_at' => now()]);
        $socialite->save();
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        $socialite = $this->userSocialites->where('type', UserSocialite::TYPE_EMAIL)->first();
        return !!$socialite->verified_at;
    }

    /**
     * 获取用户绑定微信小程序的openid
     * @return string
     */
    public function getOpenidAttribute()
    {
        if (!isset($this->attributes['openid']) || !$this->attributes['openid']) {
            $this->attributes['openid'] = data_get($this->userSocialites->where('type', UserSocialite::TYPE_WXAPP)->first(), 'account');
        }
        return $this->attributes['openid'];
    }

    public function getEmailForVerification()
    {
        return data_get($this->userSocialites->where('type', UserSocialite::TYPE_EMAIL)->first(), 'account');
    }
}
