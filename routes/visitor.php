<?php

Route::group(['prefix' => 'visitor/', 'as' => 'visitor.',], function () {
    Route::post('init', 'Visitor\ConversationController@init')->name('init');
});
