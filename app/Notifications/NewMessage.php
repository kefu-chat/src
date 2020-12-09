<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\HasPushSubscriptions;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;
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
        return $notifiable instanceof User ? [WebPushChannel::class, 'wechat-app'] : [WebPushChannel::class];
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
        return 'LHgTmtQNNOiAZ8qNL9g4y-7a_gzNX62chkju-eX44jc';
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateMessageData()
    {
        return [
            'name1' => $this->notification->sender->name, //客户名称
            'time2' => $this->notification->created_at->format('Y年m月d日 H:i:s'), //咨询时间
            'thing3' => $this->notification->type == Message::TYPE_TEXT ? $this->notification->content : '[客户发来一张图片]', //咨询内容
            'phone_number5' => optional(optional(optional($this->notification)->conversation)->visitor)->phone ?: '未获得手机号', //客户手机号
        ];
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
}
