<?php

namespace App\Broadcasting;

use App\Http\Transformers\ConversationDetailTransformer;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 新会话 Socket
 */
class ConversationIncoming implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return array_merge(
            [
                new PresenceChannel('institution.' . $this->conversation->institution->public_id),
                new PresenceChannel('enterprise.' . $this->conversation->institution->enterprise->public_id),
            ],
            !$this->conversation->user_id ?
                [
                ] : [
                    new PresenceChannel('institution.' . $this->conversation->institution->public_id . '.assigned.' . $this->conversation->user->public_id)
                ]
        );
    }

    /**
     * 事件名称
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'conversation.created';
    }

    /**
     * 发送的消息数据
     *
     * @return Message
     */
    public function broadcastWith()
    {
        $this->conversation->with(['lastMessage',]);
        return $this->conversation->setTransformer(ConversationDetailTransformer::class)->toArray();
    }
}
