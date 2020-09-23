<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationDetailTransformer;
use App\Http\Transformers\ConversationListWithOnlineStatusTransformer;
use App\Models\Conversation;
use App\Models\User;
use App\Repositories\ConversationRepository;
use Illuminate\Database\Eloquent\InvalidCastException;
use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use LogicException;
use InvalidArgumentException;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vinkla\Hashids\Facades\Hashids;

class ConversationController extends Controller
{
    use Messagingable;

    /**
     * Update the user's password.
     *
     * @param  \Illuminate\Http\Request $request
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
        if (!$offset) {
            if ($request_offset) {
                throw ValidationException::withMessages([
                    'offset' => 'offset 无效!' . $request_offset . '-' . $offset,
                ]);
            }
            $offset = 0;
        }
        $conversations = $conversationRepository->listConversations($this->user, $offset, $type, ['messages',]);

        return response()->success([
            'conversations' => $conversations->setTransformer(ConversationListWithOnlineStatusTransformer::class),
        ]);
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
}
