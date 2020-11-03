<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends RocketBaseController
{
    public function presence(Request $request)
    {
        return '{"users":[{"_id":"ZoDTNEi6yHkn7hTQc","status":"online","name":"admin","username":"admin","utcOffset":8}],"full":false,"success":true}';
    }

    public function register(Request $request)
    {
        return '{"user":{"_id":"6JWwbGCgkwCH6fkja","type":"user","status":"offline","active":true,"name":"test","username":"test","__rooms":["GENERAL"]},"success":true}';
    }

    public function login(Request $request)
    {
        return '{"status":"success","data":{"userId":"6JWwbGCgkwCH6fkja","authToken":"KPJgkPGEneHFHdN3qp5jyRgmS4NJx8M8vw-eytRmDJU","me":{"_id":"6JWwbGCgkwCH6fkja","services":{"password":{"bcrypt":"$2b$10$0z94snNWbxWveXYoRJOe2u4mwyJ8SZ7s0xhXzOP8m8nGUGHJ7JCAG"}},"emails":[{"address":"op@staff.digital-sign.cn","verified":false}],"status":"offline","active":true,"_updatedAt":"2020-11-03T06:06:52.470Z","roles":["user"],"name":"test","username":"test","avatarUrl":"https://' . $this->getApiDomain() . '/avatar/test","settings":{"preferences":{"enableAutoAway":true,"idleTimeLimit":300,"desktopNotificationRequireInteraction":false,"audioNotifications":"mentions","desktopNotifications":"all","mobileNotifications":"all","unreadAlert":true,"useEmojis":true,"convertAsciiEmoji":true,"autoImageLoad":true,"saveMobileBandwidth":true,"collapseMediaByDefault":false,"hideUsernames":false,"hideRoles":false,"hideFlexTab":false,"hideAvatars":false,"sidebarGroupByType":true,"sidebarViewMode":"medium","sidebarHideAvatar":false,"sidebarShowUnread":false,"sidebarSortby":"activity","showMessageInMainThread":false,"sidebarShowFavorites":true,"sendOnEnter":"normal","messageViewMode":0,"emailNotificationMode":"mentions","newRoomNotification":"door","newMessageNotification":"chime","muteFocusedConversations":true,"notificationsSoundVolume":100,"sidebarShowDiscussion":true}}}}}';
    }
}
