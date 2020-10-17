<?php

namespace App\Broadcasting;

use App\Http\Transformers\ConversationUserTransformer;
use App\Interfaces\ShoudWebpush;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * 客服分配 Socket, 主要是给访客端用
 */
class ConversationAssigning implements ShouldBroadcast, ShoudWebpush
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

    /**
     * 获取webpush的通知对象
     *
     * @return array
     */
    public function getWebpushNotification()
    {
        return [
            'title' => '客服接入',
            'body' => '客服接入: ' . $this->conversation->user->name,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getWebpushSubscriber()
    {
        return [$this->conversation->visitor];
    }
}
