<?php

namespace App\Http\Controllers\Visitor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\Messagingable;
use App\Http\Transformers\ConversationListTransformer;
use App\Models\Institution;
use App\Repositories\ConversationRepository;
use App\Repositories\VisitorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
            'url' => ['required', 'url',],
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

        if (!$request->headers->has('authorization')) {
            init:
            $institution = Institution::findPublicIdOrFail($institution_id);
            $visitor = $visitorRepository->init($institution, $unique_id, $name, $email, $phone, $avatar, $memo, $address);
            $conversation = $conversationRepository->initConversation($visitor, $ip, $url);
        } else {
            $visitor = $this->user;
            if (!$visitor || !$this->isVisitor()) {
                goto init;
            }
            $conversation = $visitor->conversations()->latest('id')->first();
            if (!$conversation) {
                $conversation = $conversationRepository->initConversation($visitor, $ip, $url);
            }
        }
        $visitor_token = JWTAuth::fromUser($visitor);
        $visitor->id = $visitor->public_id;

        return response()->success([
            'conversation' => $conversation->setTransformer(ConversationListTransformer::class),
            'visitor_token' => $visitor_token,
            'visitor_type' => 'Berear',
        ]);
    }
}
