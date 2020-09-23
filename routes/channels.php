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

use App\Http\Transformers\ConversationUserTransformer;
use App\Models\Conversation;
use App\Models\Enterprise;
use App\Models\Institution;
use App\Models\User;
use App\Models\Visitor;
use Spatie\Permission\Models\Permission;

/**
 * @see \App\Broadcasting\ConversationMessaging
 * @see \App\Broadcasting\ConversationAssigning
 */
Broadcast::channel('conversation.{conversation}', function ($user, Conversation $conversation) {
    if ($conversation->{[Visitor::class => 'visitor_id', User::class => 'user_id'][get_class($user)]} == $user->id) {
        if ($user instanceof User) {
            return $user->setTransformer(ConversationUserTransformer::class);
        }
        return $user;
    }
    return false;
}, ['guards' => ['visitor', 'api']]);

Broadcast::channel('institution.{institution}', function (User $user, Institution $institution) {
    if ($institution->id == $user->institution_id || ($user->hasPermissionTo(Permission::findByName('manager', 'api')) && $institution->enterprise_id = $user->enterprise_id)) {
        return $user->setTransformer(ConversationUserTransformer::class);
    }
    return false;
}, ['guards' => ['api']]);

Broadcast::channel('enterprise.{institution}', function (User $user, Enterprise $enterprise) {
    if ($user->hasPermissionTo(Permission::findByName('manager', 'api')) && $enterprise->id = $user->enterprise_id) {
        return $user->setTransformer(ConversationUserTransformer::class);
    }
    return false;
}, ['guards' => ['api']]);
