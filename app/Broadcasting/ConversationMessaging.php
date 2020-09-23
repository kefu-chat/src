<?php

namespace App\Broadcasting;

use App\Http\Transformers\MessageListTransformer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Visitor;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 消息 Socket, 访客侧和客服侧共用
 */
class ConversationMessaging implements ShouldBroadcast
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
        return new PresenceChannel('conversation.' . $this->message->conversation->public_id);
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
}
