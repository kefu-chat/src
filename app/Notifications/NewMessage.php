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

class NewMessage extends Notification implements ShouldQueue
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
        return [WebPushChannel::class];
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
}
