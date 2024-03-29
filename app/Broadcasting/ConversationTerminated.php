<?php

namespace App\Broadcasting;

use App\Http\Transformers\MessageListTransformer;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 消息 Socket, 访客侧和客服侧共用
 */
class ConversationTerminated implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var Message
     */
    protected $message;

    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation, Message $message)
    {
        $this->conversation = $conversation;
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
            new PresenceChannel('conversation.' . $this->conversation->public_id),
            new PresenceChannel('institution.' . $this->message->institution->public_id .'.assigned.' . $this->message->conversation->user->public_id),
        ] : [
            new PresenceChannel('conversation.' . $this->conversation->public_id),
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
        return 'conversation.terminated';
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
