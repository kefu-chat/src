<?php

namespace App\Repositories;

use App\Models\Conversation;
use App\Models\User;
use App\Models\Visitor;

class ConversationRepository
{
    /**
     * 拉取会话
     *
     * @param User $user
     * @param int|null $offset
     * @param string|null $type
     * @param array $has
     * @return Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation>
     */
    public function listConversations(User $user, $offset, $type, $has = [])
    {
        /**
         * @var Conversation|Builder $query
         */
        $query = app(Conversation::class);

        /**
         * @var Conversation[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Collection<int,Conversation> $conversations
         */
        $conversations = $query->with(['visitor', 'user',])
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
            ->latest()->limit(20)->get();

        return $conversations;
    }

    /**
     * 初始化会话
     *
     * @param Visitor $visitor
     * @param $ip
     * @param $url
     * @return Conversation
     */
    public function initConversation(Visitor $visitor, $ip, $url)
    {
        $conversation = new Conversation([
            'ip' => $ip,
            'url' => $url,
            'first_reply_at' => null,
            'last_reply_at' => null,
        ]);
        $conversation->institution()->associate($visitor->institution);
        $conversation->visitor()->associate($visitor);
        //$conversation->user()->associate($user);
        $conversation->save();
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
        return $conversation->save();
    }
}
