<?php

Route::group(['middleware' => 'auth:api,visitor'], function () {
    Route::get('conversation/{conversation}/messages', 'Personnel\ConversationController@listConversationMessage')->name('conversation.message.list');
    Route::post('conversation/{conversation}/send-message', 'Personnel\ConversationController@sendMessage')->name('conversation.message.send');
    Route::post('file/upload', 'FileController@upload')->name('file.upload');
    Route::post('push/subscribe', 'PushController@subscribe')->name('push.subscribe');
});
