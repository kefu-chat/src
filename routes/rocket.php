<?php

/*
|--------------------------------------------------------------------------
| 兼容 RocketChat 客户端的 API 酷游
|--------------------------------------------------------------------------
|
*/

Route::group(['middleware' => 'auth:api', 'as' => 'rocket.', 'prefix' => 'v1/',], function () {
    Route::get('info', 'InfoController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/authentication/login
    Route::post('login', 'InfoController@info')->name('login'); //https://docs.rocket.chat/api/rest-api/methods/authentication/login

    Route::group(['as' => 'channels.', 'prefix' => 'channels.',], function () {
        Route::get('info', 'ChannelController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/channels/info
        Route::get('members', 'ChannelController@members')->name('members'); //https://docs.rocket.chat/api/rest-api/methods/channels/members
        Route::get('messages', 'ChannelController@messages')->name('messages'); //https://docs.rocket.chat/api/rest-api/methods/channels/messages
        Route::get('moderators', 'ChannelController@moderators')->name('moderators'); //https://docs.rocket.chat/api/rest-api/methods/channels/moderators
        Route::post('join', 'ChannelController@join')->name('join'); //https://docs.rocket.chat/api/rest-api/methods/channels/join
        Route::post('leave', 'ChannelController@leave')->name('leave'); //https://docs.rocket.chat/api/rest-api/methods/channels/leave
        Route::post('invite', 'ChannelController@invite')->name('invite'); //https://docs.rocket.chat/api/rest-api/methods/channels/invite
        Route::post('kick', 'ChannelController@kick')->name('kick'); //https://docs.rocket.chat/api/rest-api/methods/channels/kick
    });

    Route::group(['as' => 'users.', 'prefix' => 'users.',], function () {
        Route::post('register', 'UserController@register')->name('register'); //https://docs.rocket.chat/api/rest-api/methods/users/register
        Route::post('forgotPassword', 'UserController@forgotPassword')->name('forgotPassword'); //https://docs.rocket.chat/api/rest-api/methods/users/forgotpassword
        Route::get('info', 'UserController@info')->name('info'); //https://docs.rocket.chat/api/rest-api/methods/users/info
        Route::post('update', 'UserController@update')->name('update'); //https://docs.rocket.chat/api/rest-api/methods/users/update
        Route::post('updateOwnBasicInfo', 'UserController@updateOwnBasicInfo')->name('updateOwnBasicInfo'); //https://docs.rocket.chat/api/rest-api/methods/users/updateOwnBasicInfo
        Route::post('setAvatar', 'UserController@setAvatar')->name('setAvatar'); //https://docs.rocket.chat/api/rest-api/methods/users/setavatar
        Route::post('resetAvatar', 'UserController@resetAvatar')->name('resetAvatar'); //https://docs.rocket.chat/api/rest-api/methods/users/resetavatar
        Route::get('list', 'UserController@list')->name('list'); //https://docs.rocket.chat/api/rest-api/methods/users/list
        Route::get('getStatus', 'UserController@getStatus')->name('getStatus'); //https://docs.rocket.chat/api/rest-api/methods/users/getstatus
        Route::get('getAvatar', 'UserController@getAvatar')->name('getAvatar'); //https://docs.rocket.chat/api/rest-api/methods/users/getavatar
    });
});

Route::group(['middleware' => 'guest:api'], function () {
});
