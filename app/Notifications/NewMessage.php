<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\HasPushSubscriptions;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
use Xiaohuilam\Laravel\WxappNotificationChannel\Broadcasting\WechatAppNotificationChannel;
use Xiaohuilam\Laravel\WxappNotificationChannel\Interfaces\WechatAppNotificationable;

class NewMessage extends Notification implements ShouldQueue, WechatAppNotificationable
{
    use Queueable;

    /**
     * @var Message
     */
    protected $notification;

    public function __construct(Message $notification)
    {
        $this->notification = $notification;
    }

    /**
     * 推送给浏览器
     *
     * @param HasPushSubscriptions|User|Visitor $notifiable
     */
    public function via($notifiable)
    {
        return $notifiable instanceof User ? [WebPushChannel::class, WechatAppNotificationChannel::class] : [WebPushChannel::class];
    }

    /**
     * 推送给浏览器
     *
     * @param HasPushSubscriptions|User|Visitor $notifiable
     * @param Message $notification
     * @return WebPushMessage
     */
    public function toWebPush($notifiable)
    {
        return (new WebPushMessage)
            ->title('“' . $this->notification->sender->name . '”发来了新消息!')
            ->action('查看', 'view')
            ->data(['id' => $this->notification->public_id, 'conversation_id' => $this->notification->conversation->public_id])
            //->options(['TTL' => 1000])
            //->icon('/approved-icon.png')
            //->badge()
            //->dir()
            //->lang()
            //->renotify()
            //->requireInteraction()
            ->tag($this->notification->sender->name . '(#' . $this->notification->conversation->public_id . ')')
            ->vibrate(1)
            ->{$this->notification->type === Message::TYPE_IMAGE ? 'image' : 'body'}($this->notification->content);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateId()
    {
        return $this->isValidPhone($this->notification) ? 'LHgTmtQNNOiAZ8qNL9g4y-7a_gzNX62chkju-eX44jc' : 'LHgTmtQNNOiAZ8qNL9g4y3RFTOmlMUeaPNkfs5Trte8';
    }

    protected function isValidPhone($notification)
    {
        $phone = optional(optional(optional($notification)->conversation)->visitor)->phone;
        if (preg_match('/^1[\d]{10,10}$/', $phone)) {
            return true;
        }
        if (preg_match('/^[\d]{3,4}[\d]{5,9}$/', $phone)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateMessageData()
    {
        $name = $this->notification->sender->name;
        if (!preg_match('/^[a-zA-Z0-9_\u4e00-\u9fa5]+$/', $name)) {
            $name = '访客';
        }

        return array_merge([
            'name1' => [ //客户名称
                'value' => $name,
            ],
            'time2' => [ //咨询时间
                'value' => $this->notification->created_at->format('Y年m月d日 H:i:s'),
            ],
            'thing3' => [ //咨询内容
                'value' => $this->notification->type == Message::TYPE_TEXT ? $this->notification->content : '[图片]',
            ],
        ],
        $this->isValidPhone($this->notification) ? [
            'phone_number5' => [ //客户手机号
                'value' => optional(optional(optional($this->notification)->conversation)->visitor)->phone,
            ],
        ]: []);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateMessageEmphasisKeyword()
    {
        return 'name1.DATA';
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateMessagePath()
    {
        return '/pages/chat/chat?id=' . $this->notification->conversation->public_id;
    }

    /**
     * 什么版本
     * @return string
     * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html#%E8%AF%B7%E6%B1%82%E5%8F%82%E6%95%B0
     */
    public function getMiniprogramState()
    {
        if (config('kefu.qr_url') == 'https://dev.kefu.chat') {
            return 'developer';
        }
        if (config('kefu.qr_url') == 'https://test.kefu.chat') {
            return 'trial';
        }
        return 'formal';
    }
}
