<?php

namespace App\Broadcasting;

use App\Http\Transformers\MessageListTransformer;
use App\Interfaces\ShoudWebpush;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 消息 Socket, 访客侧和客服侧共用
 */
class ConversationMessaging implements ShouldBroadcast, ShoudWebpush
{
    use InteractsWithSockets;

    /**
     * @var Message
     */
    protected $message;

    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return $this->message->conversation->user_id ? [
            new PresenceChannel('institution.' . $this->message->institution->public_id .'.assigned.' . $this->message->conversation->user->public_id),
        ] : [
            new PresenceChannel('institution.' . $this->message->institution->public_id),
        ];
    }

    /**
     * 事件名称
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'message.created';
    }

    /**
     * 发送的消息数据
     *
     * @return Message
     */
    public function broadcastWith()
    {
        return $this->message->setTransformer(MessageListTransformer::class)->toArray();
    }

    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 获取webpush的通知对象
     *
     * @return array
     */
    public function getWebpushNotification()
    {
        return [
            'title' => '您收到回复',
            'body' => '您收到回复(' . $this->message->sender->name . '): ' . ($this->message->type == Message::TYPE_TEXT ? $this->message->content : '[图片消息]'),
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getWebpushSubscriber()
    {
        return [$this->message->sender_type === User::class ? $this->message->conversation->visitor : $this->message->conversation->user];
    }
}
