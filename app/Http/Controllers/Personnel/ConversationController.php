<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationDetailTransformer;
use App\Http\Transformers\ConversationListTransformer;
use App\Models\Conversation;
use App\Models\User;
use App\Repositories\ConversationRepository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Vinkla\Hashids\Facades\Hashids;

class ConversationController extends Controller
{
    use Messagingable;

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
            'type' => ['string', 'in:assigned,unassigned'],
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
        $conversations = $conversationRepository->listConversations($this->user, $offset, $type, Conversation::STATUS_OPEN, ['messages',]);

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
            'type' => ['nullable', 'string', 'in:all,online'],
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
