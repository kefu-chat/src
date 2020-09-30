<?php

namespace App\Repositories;

use App\Broadcasting\ConversationIncoming;
use App\Broadcasting\ConversationMessaging;
use App\Http\Transformers\MessageListTransformer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Tymon\JWTAuth\Contracts\JWTSubject;

class MessageRepository
{
    /**
     * 发送消息
     *
     * @param Conversation $conversation
     * @param JWTSubject|User|Visitor $user
     * @param boolean $isUser
     * @param boolean $isVisitor
     * @param integer $type
     * @param string $content
     * @return Message
     */
    public function sendMessage(Conversation $conversation, JWTSubject $user, bool $isUser, bool $isVisitor, int $type, string $content)
    {
        /**
         * @var ConversationRepository $conversationRepository
         */
        $conversationRepository = app(ConversationRepository::class);
        if ($conversation->institution_id != $user->institution_id) {
            abort(404);
        }

        if ($isUser) {
            if (!$conversation->user_id) {
                $conversationRepository->assignConversation($conversation, $user);
            }
        }

        if ($isVisitor) {
            if ($conversation->visitor_id != $user->id) {
                abort(404);
            }

            if ($conversation->messages()->count() == 0) {
                broadcast(new ConversationIncoming($conversation));
            }
        }

        $message = new Message([
            'type' => $type,
            'content' => $content,
        ]);
        $message->conversation()->associate($conversation);
        $message->sender()->associate($user);
        $message->institution()->associate($user->institution);
        $message->save();

        $conversation->fill(['last_reply_at' => now()]);
        $conversation->save();

        broadcast(new ConversationMessaging($message));

        return $message;
    }

    /**
     * 拉取会话消息
     *
     * @param Conversation $conversation
     * @param int|null $offset
     * @return Message[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Message>
     */
    public function listConversationMessage(Conversation $conversation, $offset)
    {
        /**
         * @var Message[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Message> $messages
         */
        $messages = $conversation->messages()
            ->when($offset, function ($query) use ($offset) {
                return $query->where('id', '<', $offset);
            })
            ->take(50)
            ->orderBy('id', 'DESC')
            ->get();

        return $messages;
    }

    /**
     * 更早
     *
     * @param Conversation $conversation
     * @param int $offset
     * @return boolean
     */
    public function hasPervious(Conversation $conversation, $offset)
    {
        return !!$conversation->messages()->where('id', '<', $offset ?? -1)->first();
    }
}
