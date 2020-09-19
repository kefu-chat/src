<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationListTransformer;
use App\Repositories\ConversationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
    public function listConversation(Request $request, ConversationRepository $conversationRepository)
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
            'conversations' => $conversations->setTransformer(ConversationListTransformer::class),
        ]);
    }
}
