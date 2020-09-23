<?php

namespace App\Broadcasting;

use App\Http\Transformers\ConversationUserTransformer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 客服分配 Socket, 主要是给访客端用
 */
class ConversationAssigning implements ShouldBroadcast
{
    use InteractsWithSockets;

    /**
     * @var Conversation
     */
    protected $conversation;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct(Conversation $conversation, User $user)
    {
        $this->conversation = $conversation;
        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PresenceChannel('conversation.' . $this->conversation->public_id);
    }

    /**
     * 事件名称
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'conversation.assigned';
    }

    /**
     * 发送的消息数据
     *
     * @return Message
     */
    public function broadcastWith()
    {
        return $this->user->setTransformer(ConversationUserTransformer::class)->toArray();
    }
}
