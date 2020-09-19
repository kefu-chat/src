<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api,visitor'], function () {
    Route::post('file/upload', 'FileController@upload')->name('file.upload');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::get('user', 'Personnel\UserController@info')->name('user.info.agent');

    Route::get('conversation/list', 'Personnel\ConversationController@listConversation')->name('conversation.list.agent');
    Route::get('conversation/{conversation}/messages', 'Personnel\ConversationController@listConversationMessage')->name('conversation.message.list.agent');
    Route::post('conversation/{conversation}/send-message', 'Personnel\ConversationController@sendMessage')->name('conversation.message.send.agent');

    Route::get('institution', 'Personnel\InstitutionController@showProfile')->name('institution.profile.show');
    Route::post('institution', 'Personnel\InstitutionController@updateProfile')->name('institution.profile.update')->middleware(['can:manager']);
});

Route::group(['middleware' => 'guest:api'], function () {
    Route::post('login', 'Auth\LoginController@login')->name('login');
    Route::post('register', 'Auth\RegisterController@register');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('email/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
    Route::post('email/resend', 'Auth\VerificationController@resend');

    Route::post('oauth/{driver}', 'Auth\OAuthController@redirectToProvider');
    Route::get('oauth/{driver}/callback', 'Auth\OAuthController@handleProviderCallback')->name('oauth.callback');
});

Route::group(['prefix' => 'visitor/', 'as' => 'visitor.', ], function () {
    Route::post('init', 'Visitor\ConversationController@init')->name('init');
    Route::group(['middleware' => 'auth:visitor', ], function () {
        Route::get('conversation/{conversation}/messages', 'Personnel\ConversationController@listConversationMessage')->name('list-message');
        Route::post('conversation/{conversation}/send-message', 'Personnel\ConversationController@sendMessage')->name('send-message');
    });
});
