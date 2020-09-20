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

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::patch('settings/profile', 'Settings\ProfileController@update');
    Route::patch('settings/password', 'Settings\PasswordController@update');

    Route::get('user', 'Personnel\UserController@info')->name('user.info.agent');

    // 会话列表
    Route::get('conversation/list', 'Personnel\ConversationController@listConversation')->name('conversation.list.agent');

    // 网站管理
    Route::post('institution/create', 'Personnel\InstitutionController@store')->name('institution.create')->middleware(['can:manager']);
    Route::get('institution/{institution}', 'Personnel\InstitutionController@show')->name('institution.show');
    Route::post('institution/{institution}/update', 'Personnel\InstitutionController@update')->name('institution.update')->middleware(['can:manager']);
    Route::post('institution/{institution}/delete', 'Personnel\InstitutionController@delete')->name('institution.delete')->middleware(['can:manager']);

    // 套餐管理
    Route::get('enterprise/plan', 'Personnel\PlanController@show')->name('enterprise.plan.show');
    Route::get('enterprise/plan/upgrade/{plan}', 'Personnel\PlanController@upgrade')->name('enterprise.plan.upgrade');
});
