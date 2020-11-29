<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationDetailTransformer;
use App\Http\Transformers\ConversationListTransformer;
use App\Models\Conversation;
use App\Models\User;
use App\Repositories\ConversationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

class ConversationController extends Controller
{
    use Messagingable;

    /**
     * 统计会话数
     *
     * @param ConversationRepository $conversationRepository
     * @return \Illuminate\Http\Response
     */
    public function count(ConversationRepository $conversationRepository)
    {
        $unassigned_count = $conversationRepository->count($this->user, 'unassigned', Conversation::STATUS_OPEN, ['messages',]);
        $assigned_count = $conversationRepository->count($this->user, 'assigned', Conversation::STATUS_OPEN, ['messages',]);
        $history_count = $conversationRepository->count($this->user, 'assigned', Conversation::STATUS_CLOSED, ['messages',]);
        $online_visitor_count = $conversationRepository->countUngreetedConversations($this->user->institution, 'online');
        $offline_visitor_count = $conversationRepository->countUngreetedConversations($this->user->institution, 'offline');
        $visitor_count = $conversationRepository->countUngreetedConversations($this->user->institution, 'all');

        return response()->success([
            'unassigned_count' => $unassigned_count,
            'assigned_count' => $assigned_count,
            'history_count' => $history_count,
            'online_visitor_count' => $online_visitor_count,
            'offline_visitor_count' => $offline_visitor_count,
            'visitor_count' => $visitor_count,
        ]);
    }

    /**
     * 拉取聊天
     *
     * @param  \Illuminate\Http\Request $request
     * @param ConversationRepository $conversationRepository
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request, ConversationRepository $conversationRepository)
    {
        $request->validate([
            'offset' => ['nullable', 'string'],
            'type' => ['string', 'in:assigned,unassigned,active,history'],
        ]);

        $type = $request->input('type');
        $request_offset = $request->input('offset');
        $offset = Arr::first(Hashids::decode($request_offset));
        $status = Conversation::STATUS_OPEN;
        if ($type == 'history') {
            $type = 'assigned';
            $status = Conversation::STATUS_CLOSED;
        }

        if ($request_offset) {
            if (!$offset) {
                throw ValidationException::withMessages([
                    'offset' => 'offset 无效! CRC32校验失败' . $request_offset . '-' . $offset,
                ]);
            }
            if (crc32(Conversation::class) != Arr::last(Hashids::decode($request_offset))) {
                throw ValidationException::withMessages([
                    'offset' => 'offset 无效! CRC32校验失败' . $request_offset . '-' . $offset,
                ]);
            }
        }
        $conversations = $conversationRepository->listConversations($this->user, $offset, $type, $status, ['messages',]);

        return response()->success([
            'user_id' => $this->user->public_id,
            'institution_id' => $this->user->institution->public_id,
            'conversations' => $conversations->setTransformer(ConversationListTransformer::class),
        ]);
    }

    /**
     * 结束对话
     *
     * @param Conversation $conversation
     * @param \Illuminate\Http\Request $request
     * @param ConversationRepository $conversationRepository
     * @return \Illuminate\Http\Response
     */
    public function terminate(Conversation $conversation, Request $request, ConversationRepository $conversationRepository)
    {
        if (Conversation::STATUS_CLOSED == $conversation->status) {
            abort(400, 'The conversation has already been closed');
        }

        $conversationRepository->terminateManual($conversation, $this->user);

        return response()->success();
    }

    /**
     * 转移给同事
     *
     * @param Conversation $conversation
     * @param User $user
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function transfer(Conversation $conversation, User $user, Request $request)
    {
        if (!$this->user->hasPermissionTo(Permission::findByName('manager', 'api')) && $conversation->user_id != $this->user->id) {
            abort(404);
        }
        if ($user->enterprise_id != $this->user->enterprise_id) {
            abort(404);
        }
        if (!$user->hasPermissionTo(Permission::findByName('manager', 'api')) && $user->institution_id != $this->user->institution_id) {
            abort(404);
        }

        $conversation->user()->associate($user);
        $conversation->save();

        return response()->success([
            'conversation' => $conversation->setTransformer(ConversationDetailTransformer::class),
        ]);
    }

    /**
     * 拉取在线未咨询访客
     *
     * @param Request $request
     * @param ConversationRepository $conversationRepository
     * @return \Illuminate\Http\Response
     */
    public function listUngreeted(Request $request, ConversationRepository $conversationRepository)
    {
        $request->validate([
            'offset' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'in:all,online,offline'],
        ]);
        $type = $request->input('type');
        $request_offset = $request->input('offset');
        $offset = Arr::first(Hashids::decode($request_offset));
        if ($request_offset) {
            if (!$offset) {
                throw ValidationException::withMessages([
                    'offset' => 'offset 无效! CRC32校验失败' . $request_offset . '-' . $offset,
                ]);
            }
            if (crc32(Conversation::class) != Arr::last(Hashids::decode($request_offset))) {
                throw ValidationException::withMessages([
                    'offset' => 'offset 无效! CRC32校验失败' . $request_offset . '-' . $offset,
                ]);
            }
        }

        if (!$type) {
            $type = 'online';
        }

        $conversations = $conversationRepository->listUngreetedConversations($this->user->institution, $offset, $type);

        return response()->success([
            'user_id' => $this->user->public_id,
            'institution_id' => $this->user->institution->public_id,
            'conversations' => $conversations->setTransformer(ConversationListTransformer::class),
        ]);
    }
}
