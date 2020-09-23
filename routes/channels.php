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
use App\Models\User;
use App\Models\Visitor;

/**
 * @see \App\Broadcasting\ConversationMessaging
 * @see \App\Broadcasting\ConversationAssigning
 */
Broadcast::channel('conversation.{conversation}', function ($user, Conversation $conversation) {
    return $conversation->{[Visitor::class => 'visitor_id', User::class => 'user_id'][get_class($user)]} == $user->id;
}, ['guards' => ['visitor', 'api']]);
