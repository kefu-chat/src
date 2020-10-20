<?php

Route::group(['prefix' => 'visitor/', 'as' => 'visitor.',], function () {
    Route::get('config/{institution}', 'Visitor\ConversationController@getConfig')->name('get.config');
    Route::post('init', 'Visitor\ConversationController@init')->name('init');
});
