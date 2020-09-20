<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Support\Arr;
use Vinkla\Hashids\Facades\Hashids;

/**
 * @see \App\Broadcasting\ConversationMessaging
 */
Broadcast::channel('conversation.{id}.messaging', function ($user, $id) {
    $decoded = Hashids::decode($id);
    if (Arr::last($decoded) != crc32(Conversation::class)) {
        abort(403, '路由 ID 拼错了， CRC32校验失败: ' . Conversation::class);
    }
    $id = Arr::first($decoded);

    $conversation = Conversation::findOrFail($id);
    return $conversation->{[Visitor::class => 'visitor_id', User::class => 'user_id'][get_class($user)]} == $user->id;
}, ['guards' => ['visitor', 'api']]);
