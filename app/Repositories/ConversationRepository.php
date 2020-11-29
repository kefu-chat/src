<?php

namespace App\Repositories;

use App\Broadcasting\ConversationAssigning;
use App\Broadcasting\ConversationIncoming;
use App\Broadcasting\ConversationTerminated;
use App\Broadcasting\VisitorIncoming;
use App\Models\Conversation;
use App\Models\Institution;
use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;

class ConversationRepository
{
    /**
     * 统计网站下未打过招呼的会话个数
     * @param Institution $institution
     * @param string|null $type
     * @return int
     */
    public function countUngreetedConversations(Institution $institution, $type)
    {
        return $institution->conversations()->whereDoesntHave('messages')
            ->when($type == 'online', function ($query) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where('online_status', true);
            })
            ->when($type == 'offline', function ($query) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where('online_status', false);
            })
            ->count();
    }

    /**
     * 拉取网站下未打过招呼的会话
     * @param Institution $institution
     * @param int|null $offset
     * @param string|null $type
     * @return Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation>
     */
    public function listUngreetedConversations(Institution $institution, $offset, $type)
    {
        /**
         * @var Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation> $conversations
         */
        $conversations = $institution->conversations()->whereDoesntHave('messages')
            ->when($offset, function ($query) use ($offset) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where('id', '<', $offset);
            })
            ->when($type == 'online', function ($query) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where('online_status', true);
            })
            ->when($type == 'offline', function ($query) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where('online_status', false);
            })
            ->latest()
            ->limit(20)
            ->get();

        return $conversations;
    }

    /**
     * 统计
     *
     * @param User $user
     * @param string $type
     * @param bool $status 开关状态
     * @param array $has
     * @return int
     */
    public function count(User $user, $type, $status = Conversation::STATUS_OPEN, $has = [])
    {
        /**
         * @var Conversation|Builder $query
         */
        $query = app(Conversation::class);

        return $query
            ->where('status', $status)
            ->when($user, function ($query) use ($user) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->whereHas('institution', function ($query) use ($user) {
                    return $query->where('id', $user->institution_id);
                });
            })
            ->when($type == 'unassigned', function ($query) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where(function ($query) {
                    /**
                     * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                     */
                    return $query->where('user_id', 0)
                        ->orWhereNull('user_id');
                });
            }, function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when(count($has), function ($query) use ($has) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                foreach ($has as $needHas) {
                    $query = $query->whereHas($needHas);
                }
                return $query;
            })
            ->count();
    }

    /**
     * 拉取会话
     *
     * @param User $user
     * @param int|null $offset
     * @param string|null $type
     * @param bool $status 开关状态
     * @param array $has
     * @return Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation>
     */
    public function listConversations(User $user, $offset, $type, $status = Conversation::STATUS_OPEN, $has = [])
    {
        /**
         * @var Conversation|Builder $query
         */
        $query = app(Conversation::class);

        /**
         * @var Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation> $conversations
         */
        $conversations = $query->with(['visitor', 'user', 'lastMessage',])
            ->where('status', $status)
            ->when($user, function ($query) use ($user) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->whereHas('institution', function ($query) use ($user) {
                    return $query->where('id', $user->institution_id);
                });
            })
            ->when($type == 'unassigned', function ($query) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where(function ($query) {
                    /**
                     * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                     */
                    return $query->where('user_id', 0)
                        ->orWhereNull('user_id');
                });
            })
            ->when($type == 'assigned' || $type == 'history', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->when($type == 'active', function ($query) use ($user) {
                return $query->where(function ($query) use ($user) {
                    return $query->where('user_id', $user->id)
                        ->orWhere('user_id', 0)
                        ->orWhereNull('user_id');
                });
            })
            ->when($offset, function ($query) use ($offset) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->where('id', '<', $offset);
            })
            ->when(count($has), function ($query) use ($has) {
                /**
                 * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                foreach ($has as $needHas) {
                    $query = $query->whereHas($needHas);
                }
                return $query;
            })
            ->orderByRaw('last_reply_at DESC, id DESC, created_at DESC')->limit(20)->get();

        return $conversations;
    }

    /**
     * 初始化会话
     *
     * @param Visitor $visitor
     * @param string $ip
     * @param string $url
     * @param string $userAgent
     * @param array<int,string> $languages
     * @param string $title
     * @param string $referer
     * @return Conversation
     */
    public function initConversation(Visitor $visitor, $ip, $url, $userAgent, $languages, $title, $referer)
    {
        $conversation = new Conversation([
            'ip' => $ip,
            'url' => $url,
            'userAgent' => $userAgent,
            'languages' => $languages,
            'title' => $title,
            'referer' => $referer,
            'first_reply_at' => null,
            'last_reply_at' => null,
        ]);
        $conversation->institution()->associate($visitor->institution);
        $conversation->visitor()->associate($visitor);
        // @TODO: 自动分配
        //$conversation->user()->associate($user);
        $conversation->save();

        //@TODO:
        broadcast(new VisitorIncoming($conversation));

        return $conversation;
    }

    /**
     * 将会话转移给客服
     *
     * @param Conversation $conversation
     * @param User $user
     * @return bool
     */
    public function assignConversation(Conversation $conversation, User $user)
    {
        $conversation->user()->associate($user);
        $conversation->save();
        broadcast(new ConversationAssigning($conversation, $user));
        return $conversation;
    }

    /**
     * 超时终止对话
     * @param Conversation $conversation
     * @return Conversation
     */
    public function terminateTimeout(Conversation $conversation)
    {
        $message = null;
        $user = $conversation->user;
        if (!$user) {
            if ($conversation->institution->users->count()) {
                $user = $conversation->institution->users->random();
            } else {
                $user = $conversation->institution->enterprise->managers->random();
            }
        }

        /**
         * @var MessageRepository $messageRepository
         */
        $messageRepository = app(MessageRepository::class);
        if ($conversation->messages()->count()) {
            $message = $messageRepository->sendMessage($conversation, $user, true, false, Message::TYPE_TEXT, $conversation->institution->terminate_timeout);
        }

        $conversation->fill(['status' => Conversation::STATUS_CLOSED]);
        $conversation->save();

        if ($message) {
            broadcast(new ConversationTerminated($conversation, $message));
        }

        return $conversation;
    }

    /**
     * 终止对话
     * @param Conversation $conversation
     * @param User $user
     * @return Conversation
     */
    public function terminateManual(Conversation $conversation, User $user)
    {
        $message = null;

        /**
         * @var MessageRepository $messageRepository
         */
        $messageRepository = app(MessageRepository::class);
        if ($conversation->messages()->count()) {
            $message = $messageRepository->sendMessage($conversation, $user, true, false, Message::TYPE_TEXT, $conversation->institution->terminate_manual);
        }

        $conversation->fill(['status' => Conversation::STATUS_CLOSED]);
        $conversation->save();

        if ($message) {
            broadcast(new ConversationTerminated($conversation, $message));
        }

        return $conversation;
    }

  /**
   * 恢复对话
   * @param Conversation $conversation
   * @param Visitor $visitor
   * @return Conversation
   */
    public function reopen(Conversation $conversation, Visitor $visitor)
    {
        $conversation->fill(['status' => Conversation::STATUS_OPEN]);
        $conversation->save();
        broadcast(new ConversationIncoming($conversation));
        return $conversation;
    }
}
