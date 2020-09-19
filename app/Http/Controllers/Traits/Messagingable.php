<?php

namespace App\Http\Controllers\Traits;

use App\Broadcasting\ConversationMessaging;
use App\Http\Transformers\ConversationDetailTransformer;
use App\Http\Transformers\MessageListTransformer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @property User $user
 */
trait Messagingable
{
    /**
     * 身为客服
     *
     * @return boolean
     */
    protected function isUser()
    {
        return is_object($this->user) && $this->user instanceof User;
    }

    /**
     * 身为访客
     *
     * @return boolean
     */
    protected function isVisitor()
    {
        return is_object($this->user) && $this->user instanceof Visitor;
    }

    /**
     * 拉取会话的消息
     *
     * @param ConversationRepository $messageRepository
     * @param ConversationRepository $conversationRepository
     * @param Conversation $conversation
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function listConversationMessage(MessageRepository $messageRepository, ConversationRepository $conversationRepository, Conversation $conversation, Request $request)
    {
        $request->validate([
            'offset' => ['nullable', 'string'],
        ]);
        $request_offset = $request->input('offset');
        $offset = Arr::first(Hashids::decode($request_offset));
        if (!$offset) {
            if ($request_offset) {
                throw ValidationException::withMessages([
                    'offset' => 'offset 无效!' . $request_offset . '-' . $offset,
                ]);
            }
            $offset = 999999;
        }
        $has_previous = false;

        if ($conversation->institution_id != $this->user->institution_id) {
            abort(404);
        }

        if ($this->isUser()) {
            if (!$conversation->user_id) {
                $conversationRepository->assignConversation($conversation, $this->user);
            }
        }

        if ($this->isVisitor()) {
            if ($conversation->visitor_id != $this->user->id) {
                abort(404);
            }
        }

        $messages = $messageRepository->listConversationMessage($conversation, $offset);
        $has_previous = $messageRepository->hasPervious($conversation, $messages->min('id'));
        $messages->load(['sender',]);
        $messages = $messages->sortBy('id')->values();
        $conversation->load(['user', 'visitor',]);

        return response()->success([
            'conversation' => $conversation->setTransformer(ConversationDetailTransformer::class),
            'has_previous' => $has_previous,
            'messages' => $messages->setTransformer(MessageListTransformer::class),
        ]);
    }

    /**
     * 发送消息
     *
     * @param Conversation $conversation
     * @param Request $request
     * @param ConversationRepository $conversationRepository
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Conversation $conversation, Request $request, ConversationRepository $conversationRepository)
    {
        $request->validate($rules = [
            'type' => ['required', 'int', 'in:' . collect(Message::TYPE_MAP)->keys()->implode(','),],
            'content' => ['required', 'string'],
        ]);

        if ($conversation->institution_id != $this->user->institution_id) {
            abort(404);
        }

        if ($this->isUser()) {
            if (!$conversation->user_id) {
                $conversationRepository->assignConversation($conversation, $this->user);
            }
        }

        if ($this->isVisitor()) {
            if ($conversation->visitor_id != $this->user->id) {
                abort(404);
            }
        }

        $message = new Message($request->only(collect($rules)->keys()->values()->toArray()));
        $message->conversation()->associate($conversation);
        $message->sender()->associate($this->user);
        $message->institution()->associate($this->user->institution);
        $message->save();

        broadcast(new ConversationMessaging($message));

        return response()->success([
            'message' => $message->setTransformer(MessageListTransformer::class),
        ]);
    }
}
