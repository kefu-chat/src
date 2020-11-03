<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends RocketBaseController
{
    public function presence(Request $request)
    {
        return response()->json([
            'users' => [
                [
                    '_id' => 'ZoDTNEi6yHkn7hTQc',
                    'status' => 'online',
                    'name' => 'admin',
                    'username' => 'admin',
                    'utcOffset' => 8,
                ],
            ],
            'full' => false,
            'success' => true,
        ]);
    }

    public function register(Request $request)
    {
        return response()->json([
            'user' => [
                '_id' => '6JWwbGCgkwCH6fkja',
                'type' => 'user',
                'status' => 'offline',
                'active' => true,
                'name' => 'test',
                'username' => 'test',
                '__rooms' => ['GENERAL',],
            ],
            'success' => true,
        ]);
    }

    public function login(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'userId' => 'ZoDTNEi6yHkn7hTQc',
                'authToken' => 'MB5Co7I46nAEh0QoGeWJtHXeFN0kRA1QBIidIMKaT8H',
                'me' => [
                    '_id' => 'ZoDTNEi6yHkn7hTQc',
                    'services' => [
                        'password' => [
                            'bcrypt' => '$2b$10$t07WtgGGpqbtSj.PpCx2GebHdaMhcoQnVCVpqyhNvnLhs/VNn4ihS',
                        ],
                        'email2fa' => [
                            'enabled' => true,
                        ],
                    ],
                    'emails' => [
                        [
                            'address' => '88436812@qq.com',
                            'verified' => false,
                        ],
                    ],
                    'status' => 'offline',
                    'active' => true,
                    '_updatedAt' => '2020-11-03T14:12:24.324Z',
                    'roles' => [
                        'admin',
                    ],
                    'name' => 'admin',
                    'username' => 'admin',
                    'statusConnection' => 'offline',
                    'utcOffset' => 8,
                    'settings' => [
                        'preferences' => [
                            'enableAutoAway' => true,
                            'idleTimeLimit' => 300,
                            'desktopNotificationRequireInteraction' => false,
                            'audioNotifications' => 'mentions',
                            'desktopNotifications' => 'all',
                            'mobileNotifications' => 'all',
                            'unreadAlert' => true,
                            'useEmojis' => true,
                            'convertAsciiEmoji' => true,
                            'autoImageLoad' => true,
                            'saveMobileBandwidth' => true,
                            'collapseMediaByDefault' => false,
                            'hideUsernames' => false,
                            'hideRoles' => false,
                            'hideFlexTab' => false,
                            'hideAvatars' => false,
                            'sidebarGroupByType' => true,
                            'sidebarViewMode' => 'medium',
                            'sidebarHideAvatar' => false,
                            'sidebarShowUnread' => false,
                            'sidebarSortby' => 'activity',
                            'showMessageInMainThread' => false,
                            'sidebarShowFavorites' => true,
                            'sendOnEnter' => 'normal',
                            'messageViewMode' => 0,
                            'emailNotificationMode' => 'mentions',
                            'newRoomNotification' => 'door',
                            'newMessageNotification' => 'chime',
                            'muteFocusedConversations' => true,
                            'notificationsSoundVolume' => 100,
                            'sidebarShowDiscussion' => true,
                            'language' => 'zh-CN',
                        ],
                    ],
                    'language' => 'zh-CN',
                    'avatarUrl' => 'https://' . $this->getApiDomain() . '/avatar/admin',
                ],
            ],
        ]);
    }
}
