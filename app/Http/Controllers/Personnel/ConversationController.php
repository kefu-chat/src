<?php

namespace App\Http\Controllers\Personnel;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationListTransformer;
use App\Repositories\ConversationRepository;
use Illuminate\Http\Request;

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
            'offset' => ['integer', 'nullable',],
            'type' => ['string', 'in:assigned,unassigned'],
        ]);

        $offset = $request->input('offset', 0);
        $type = $request->input('type');
        $conversations = $conversationRepository->listConversations($this->user, $offset, $type, ['messages',]);

        return response()->success([
            'conversations' => $conversations->setTransformer(ConversationListTransformer::class),
        ]);
    }
}
