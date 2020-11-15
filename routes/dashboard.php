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
    Route::post('captcha', 'CaptchaController@svg');

    Route::post('login-via-miniapp', 'Auth\LoginController@loginViaMiniApp')->name('login.via.miniapp');
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
    Route::get('conversation/count', 'Personnel\ConversationController@count')->name('conversation.count');
    Route::get('conversation/list', 'Personnel\ConversationController@list')->name('conversation.list');
    Route::get('conversation/{conversation}/transfer/{user}', 'Personnel\ConversationController@transfer')->name('conversation.transfer');
    Route::post('conversation/{conversation}/terminate', 'Personnel\ConversationController@terminate')->name('conversation.terminate');
    Route::get('conversation/list-ungreeted', 'Personnel\ConversationController@listUngreeted')->name('conversation.list-ungreeted');


    // 网站管理
    Route::get('institution/list', 'Personnel\InstitutionController@list')->name('institution.list');
    Route::post('institution/create', 'Personnel\InstitutionController@store')->name('institution.create')->middleware(['can:manager']);
    Route::get('institution/{institution}/show', 'Personnel\InstitutionController@show')->name('institution.show');
    Route::post('institution/{institution}/update', 'Personnel\InstitutionController@update')->name('institution.update')->middleware(['can:manager']);
    Route::post('institution/{institution}/delete', 'Personnel\InstitutionController@delete')->name('institution.delete')->middleware(['can:manager']);

    // 企业资料
    Route::get('enterprise', 'Personnel\EnterpriseController@show')->name('enterprise.show');
    Route::post('enterprise/update', 'Personnel\EnterpriseController@update')->name('enterprise.update');
    Route::get('enterprise/name-suggest', 'Personnel\EnterpriseController@search')->name('enterprise.suggest');
    Route::get('enterprise/name-suggest-detail', 'Personnel\EnterpriseController@searchDetail')->name('enterprise.suggest.detail');

    // 套餐管理
    Route::get('enterprise/plan', 'Personnel\PlanController@show')->name('enterprise.plan.show');
    Route::get('enterprise/plan/upgrade/order/list', 'Personnel\PlanController@orderList')->name('enterprise.plan.order-list');
    Route::post('enterprise/plan/upgrade/{plan}', 'Personnel\PlanController@upgrade')->name('enterprise.plan.upgrade');
    Route::post('enterprise/plan/upgrade/{order}/pay/alipay', 'Personnel\PlanController@alipay')->name('enterprise.plan.upgrade.alipay');
    Route::post('enterprise/plan/upgrade/{order}/pay/wechatpay', 'Personnel\PlanController@wechatpay')->name('enterprise.plan.upgrade.wechatpay');
    Route::post('enterprise/plan/upgrade/{order}/cancel', 'Personnel\PlanController@cancel')->name('enterprise.plan.upgrade.cancel');

    // 地理位置
    Route::get('location/list', 'LocationController@list')->name('location.list');

    // 访客编辑
    Route::post('visitor/{visitor}/update', 'Personnel\VisitorController@update')->name('visitor.update');

    // 员工管理
    Route::get('institution/{institution}/employee/list', 'Personnel\EmployeeController@list')->name('employee.list');
    Route::post('institution/{institution}/employee/create', 'Personnel\EmployeeController@store')->name('employee.create')->middleware(['can:manager']);
    Route::get('institution/{institution}/employee/{user}/show', 'Personnel\EmployeeController@show')->name('employee.show');
    Route::post('institution/{institution}/employee/{user}/update', 'Personnel\EmployeeController@update')->name('employee.update')->middleware(['can:manager']);
    Route::post('institution/{institution}/employee/{user}/deactivate', 'Personnel\EmployeeController@deactivate')->name('employee.deactivate')->middleware(['can:manager']);
    Route::post('institution/{institution}/employee/{user}/activate', 'Personnel\EmployeeController@activate')->name('employee.activate')->middleware(['can:manager']);
    Route::post('institution/{institution}/employee/{user}/change-password', 'Personnel\EmployeeController@changePassword')->name('employee.change-password')->middleware(['can:manager']);
    Route::post('institution/{institution}/employee/{user}/change-permission', 'Personnel\EmployeeController@changePermission')->name('employee.change-permission')->middleware(['can:manager']);

    Route::get('security/binding/{type}', 'Settings\SecurityController@bind')->name('security.bind');
});
