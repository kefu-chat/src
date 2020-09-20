<?php

namespace App\Repositories;

use App\Models\Conversation;

class MessageRepository
{
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
