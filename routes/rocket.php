<?php

/*
|--------------------------------------------------------------------------
| 兼容 RocketChat 客户端的 API 酷游
|--------------------------------------------------------------------------
|
*/

use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;

WebSocketsRouter::webSocket('/websocket', \App\Socket\Handler\RocketHandler::class);

Route::group(['as' => 'rocket.',], function () {
    Route::group(['prefix' => 'api',], function () {
        Route::get('info', 'InfoController@info')->name('info');

        Route::group(['prefix' => 'v1/',], function () {
            Route::get('info', 'InfoController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/authentication/login
            Route::get('commands.list', 'InfoController@commandsList')->name('commands.list');
            Route::get('roles.list', 'InfoController@rolesList')->name('roles.list');
            Route::get('emoji-custom.list', 'InfoController@emojiCustomList')->name('emoji-custom.list');

            Route::group(['as' => 'settings.',], function () {
                Route::get('settings.oauth', 'SettingController@oauth')->name('oauth');
                Route::get('settings.public', 'SettingController@public')->name('public');
            });
        });
    });

    Route::group(['prefix' => 'avatar',], function () {
        Route::get('admin', 'AvatarController@admin')->name('avatar.admin');
        Route::get('@general', 'AvatarController@general')->name('avatar.general');
        Route::get('@chat', 'AvatarController@chat')->name('avatar.chat');
    });
});

Route::group(['middleware' =>'guest:api', 'as' => 'rocket.', 'prefix' => 'api/v1/',], function () {
    Route::post('login', 'UserController@login')->name('login'); //https://docs.rocket.chat/api/rest-api/methods/authentication/login

    Route::group(['as' => 'users.',], function () {
        Route::post('users.register', 'UserController@register')->name('register'); //https://docs.rocket.chat/api/rest-api/methods/users/register
        Route::post('users.forgotPassword', 'UserController@forgotPassword')->name('forgotPassword'); //https://docs.rocket.chat/api/rest-api/methods/users/forgotpassword
    });
});

Route::group(['middleware' => 'auth:api', 'as' => 'rocket.', 'prefix' => 'api/v1/',], function () {
    Route::group(['as' => 'method.call.', 'prefix' => 'method.call.',], function () {
        Route::post('license:getModules', 'MethodCallController@licenseGetModules')->name('license.getModules'); //https://docs.rocket.chat/api/rest-api/methods/channels/license:getModules
    });

    Route::group(['as' => 'channels.',], function () {
        Route::get('channels.list', 'ChannelController@list')->name('list'); //https://docs.rocket.chat/api/rest-api/methods/channels/list
        Route::get('channels.list.joined', 'ChannelController@listJoined')->name('listJoined'); //https://docs.rocket.chat/api/rest-api/methods/channels/list-joined
        Route::get('channels.info', 'ChannelController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/channels/info
        Route::get('channels.members', 'ChannelController@members')->name('members'); //https://docs.rocket.chat/api/rest-api/methods/channels/members
        Route::get('channels.messages', 'ChannelController@messages')->name('messages'); //https://docs.rocket.chat/api/rest-api/methods/channels/messages
        Route::get('channels.history', 'ChannelController@history')->name('history'); //https://docs.rocket.chat/api/rest-api/methods/channels/history
        Route::get('channels.moderators', 'ChannelController@moderators')->name('moderators'); //https://docs.rocket.chat/api/rest-api/methods/channels/moderators
        Route::post('channels.join', 'ChannelController@join')->name('join'); //https://docs.rocket.chat/api/rest-api/methods/channels/join
        Route::post('channels.leave', 'ChannelController@leave')->name('leave'); //https://docs.rocket.chat/api/rest-api/methods/channels/leave
        Route::post('channels.invite', 'ChannelController@invite')->name('invite'); //https://docs.rocket.chat/api/rest-api/methods/channels/invite
        Route::post('channels.kick', 'ChannelController@kick')->name('kick'); //https://docs.rocket.chat/api/rest-api/methods/channels/kick
    });

    Route::group(['as' => 'groups.',], function () {
        Route::get('groups.list', 'GroupController@list')->name('list'); //https://docs.rocket.chat/api/rest-api/methods/groups/list
        Route::get('groups.list.all', 'GroupController@listAll')->name('listall'); //https://docs.rocket.chat/api/rest-api/methods/groups/listall
        Route::get('groups.info', 'GroupController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/groups/info
        Route::get('groups.members', 'GroupController@members')->name('members'); //https://docs.rocket.chat/api/rest-api/methods/groups/members
        Route::get('groups.messages', 'GroupController@messages')->name('messages'); //https://docs.rocket.chat/api/rest-api/methods/groups/messages
        Route::get('groups.history', 'GroupController@history')->name('history'); //https://docs.rocket.chat/api/rest-api/methods/groups/history
        Route::get('groups.moderators', 'GroupController@moderators')->name('moderators'); //https://docs.rocket.chat/api/rest-api/methods/groups/moderators
        Route::post('groups.create', 'GroupController@create')->name('create'); //https://docs.rocket.chat/api/rest-api/methods/groups/create
        Route::post('groups.delete', 'GroupController@delete')->name('delete'); //https://docs.rocket.chat/api/rest-api/methods/groups/delete
        Route::post('groups.rename', 'GroupController@rename')->name('rename'); //https://docs.rocket.chat/api/rest-api/methods/groups/rename
        Route::post('groups.leave', 'GroupController@leave')->name('leave'); //https://docs.rocket.chat/api/rest-api/methods/groups/leave
        Route::post('groups.invite', 'GroupController@invite')->name('invite'); //https://docs.rocket.chat/api/rest-api/methods/groups/invite
        Route::post('groups.kick', 'GroupController@kick')->name('kick'); //https://docs.rocket.chat/api/rest-api/methods/groups/kick
    });

    Route::group(['as' => 'chat.',], function () {
        Route::get('chat.syncMessages', 'ChatController@syncMessages')->name('syncMessages'); //https://docs.rocket.chat/api/rest-api/methods/chat/syncMessages
        Route::get('chat.sendMessages', 'ChatController@sendMessages')->name('sendMessages'); //https://docs.rocket.chat/api/rest-api/methods/chat/sendMessages
    });

    Route::group(['as' => 'rooms.',], function () {
        Route::get('rooms.get', 'RoomController@get')->name('get'); //https://docs.rocket.chat/api/rest-api/methods/rooms/get
    });

    Route::group(['as' => 'users.',], function () {
        Route::get('users.info', 'UserController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/users/info
        Route::post('users.update', 'UserController@update')->name('update'); //https://docs.rocket.chat/api/rest-api/methods/users/update
        Route::post('users.updateOwnBasicInfo', 'UserController@updateOwnBasicInfo')->name('updateOwnBasicInfo'); //https://docs.rocket.chat/api/rest-api/methods/users/updateOwnBasicInfo
        Route::post('users.setAvatar', 'UserController@setAvatar')->name('setAvatar'); //https://docs.rocket.chat/api/rest-api/methods/users/setavatar
        Route::post('users.resetAvatar', 'UserController@resetAvatar')->name('resetAvatar'); //https://docs.rocket.chat/api/rest-api/methods/users/resetavatar
        Route::get('users.list', 'UserController@list')->name('list'); //https://docs.rocket.chat/api/rest-api/methods/users/list
        Route::get('users.getStatus', 'UserController@getStatus')->name('getStatus'); //https://docs.rocket.chat/api/rest-api/methods/users/getstatus
        Route::get('users.getAvatar', 'UserController@getAvatar')->name('getAvatar'); //https://docs.rocket.chat/api/rest-api/methods/users/getavatar
        Route::get('users.presence', 'UserController@presence')->name('presence'); //https://docs.rocket.chat/api/rest-api/methods/users/presence
    });

    Route::group(['as' => 'subscriptions.',], function () {
        Route::get('subscriptions.get', 'SubscriptionController@get')->name('get'); //https://docs.rocket.chat/api/rest-api/methods/subscriptions/get
        Route::post('subscriptions.read', 'SubscriptionController@read')->name('read'); //https://docs.rocket.chat/api/rest-api/methods/subscriptions/read
    });

    Route::group(['as' => 'permissions.',], function () {
        Route::get('permissions.listAll', 'PermissionsController@listAll')->name('listAll'); //https://docs.rocket.chat/api/rest-api/methods/permissions/listall
    });
});
