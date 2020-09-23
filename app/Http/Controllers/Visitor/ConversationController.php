<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationDetailTransformer;
use App\Http\Transformers\ConversationListTransformer;
use App\Models\Conversation;
use App\Models\Institution;
use App\Models\User;
use App\Models\Visitor;
use App\Repositories\ConversationRepository;
use App\Repositories\VisitorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ConversationController extends Controller
{
    use Messagingable;

    /**
     * 初始化访客和会话
     *
     * @param Request $request
     * @param VisitorRepository $visitorRepository
     * @param ConversationRepository $conversationRepository
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function init(Request $request, VisitorRepository $visitorRepository, ConversationRepository $conversationRepository)
    {
        $request->validate([
            'institution_id' => ['required', 'string',],
            'unique_id' => ['required', 'string'],
            'name' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string'],
            'memo' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'userAgent' => ['required', 'string',],
            'languages' => ['required', 'array',],
            'url' => ['required', 'string',],
            'title' => ['nullable', 'string',],
            'languages.*' => ['required', 'string',],
        ]);
        $ip = $request->getClientIp();
        $institution_id = $request->input('institution_id');
        $unique_id = $request->input('unique_id');
        $name = $request->input('name') ?? Str::upper($unique_id);
        $email = $request->input('email');
        $phone = $request->input('phone');
        $avatar = $request->input('avatar');
        $memo = $request->input('memo');
        $address = $request->input('address');
        $userAgent = $request->input('userAgent');
        $languages = $request->input('languages');
        $url = $request->input('url');
        $title = $request->input('title');
        if (!Str::startsWith($url, ['https://', 'http://', 'file://'])) {
            throw ValidationException::withMessages([
                'url' => 'The url format is invalid.',
            ]);
        }

        if (!$request->headers->has('authorization')) {
            init:
            $institution = Institution::findPublicIdOrFail($institution_id);
            $visitor = $visitorRepository->init($institution, $unique_id, $name, $email, $phone, $avatar, $memo, $address);
        } else {
            $visitor = $this->user;
            if (!$visitor || !$this->isVisitor()) {
                goto init;
            }
        }
        $conversation = $visitor->conversations()->latest('id')->first();
        if (!$conversation) {
            $conversation = $conversationRepository->initConversation($visitor, $ip, $url);
        }
        $visitor_token = JWTAuth::fromUser($visitor);

        return response()->success([
            'conversation' => $conversation->setTransformer(ConversationDetailTransformer::class),
            'visitor_token' => $visitor_token,
            'visitor_type' => 'Berear',
        ]);
    }

    public function leave(Request $request)
    {
        $channel = $request->input('channel_name');
        $user = $request->input('member');

        $conversation = Conversation::findPublicIdOrFail(Arr::last(explode('presence-conversation.', $channel)));
        $visitor = Visitor::findPublicIdOrFail(data_get($user, 'id'));

        $conversation->offline($visitor);

        return response()->success();
    }
}
