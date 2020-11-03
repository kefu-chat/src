<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InfoController extends RocketBaseController
{
    public function info(Request $request)
    {
        return response()->json([
            'info' => [
                'version' => '3.7.1',
            ],
            'version' => '3.7.1',
            'success' => true,
        ]);
    }

    public function login(Request $request)
    {
        return response()->json([
            "status" => "success",
            "data" => [
                "userId" => "ZoDTNEi6yHkn7hTQc",
                "authToken" => "aIPZH9uTQO0d4N4GAvMrqow5y0V-qLB0_QC-JxmQCEN",
                "me" => [
                    "_id" => "ZoDTNEi6yHkn7hTQc",
                    "services" => [
                        "password" => [
                            "bcrypt" => '$2y$10$O1LsztJ7iF9Vu4tsnaoOuuYz6XR9YSmsC7LzuSy4qOde9Z1e0.mbq',
                        ],
                        "email2fa" => [
                            "enabled" => true
                        ]
                    ],
                    "emails" => [[
                        "address" => "88436812@qq.com",
                        "verified" => false
                    ]],
                    "status" => "offline",
                    "active" => true,
                    "_updatedAt" => "2020-11-03T02:17:15.730Z",
                    "roles" => ["admin"],
                    "name" => "admin",
                    "username" => "admin",
                    "statusConnection" => "offline",
                    "utcOffset" => 8,
                    "settings" => [
                        "preferences" => [
                            "enableAutoAway" => true,
                            "idleTimeLimit" => 300,
                            "desktopNotificationRequireInteraction" => false,
                            "audioNotifications" => "mentions",
                            "desktopNotifications" => "all",
                            "mobileNotifications" => "all",
                            "unreadAlert" => true,
                            "useEmojis" => true,
                            "convertAsciiEmoji" => true,
                            "autoImageLoad" => true,
                            "saveMobileBandwidth" => true,
                            "collapseMediaByDefault" => false,
                            "hideUsernames" => false,
                            "hideRoles" => false,
                            "hideFlexTab" => false,
                            "hideAvatars" => false,
                            "sidebarGroupByType" => true,
                            "sidebarViewMode" => "medium",
                            "sidebarHideAvatar" => false,
                            "sidebarShowUnread" => false,
                            "sidebarSortby" => "activity",
                            "showMessageInMainThread" => false,
                            "sidebarShowFavorites" => true,
                            "sendOnEnter" => "normal",
                            "messageViewMode" => 0,
                            "emailNotificationMode" => "mentions",
                            "newRoomNotification" => "door",
                            "newMessageNotification" => "chime",
                            "muteFocusedConversations" => true,
                            "notificationsSoundVolume" => 100,
                            "sidebarShowDiscussion" => true,
                            "language" => "zh-CN"
                        ]
                    ],
                    "language" => "zh-CN",
                    "avatarUrl" => "https://" . $this->getApiDomain() . "/avatar/admin"
                ]
            ]
        ]);
    }

    public function commandsList()
    {
        return '{"commands":[{"command":"slackbridge-import","clientOnly":false,"providesPreview":false},{"command":"archive","params":"#channel","description":"Archive","permission":"archive-room","clientOnly":false,"providesPreview":false},{"command":"gimme","params":"your_message_optional","description":"Slash_Gimme_Description","clientOnly":false,"providesPreview":false},{"command":"lennyface","params":"your_message_optional","description":"Slash_LennyFace_Description","clientOnly":false,"providesPreview":false},{"command":"shrug","params":"your_message_optional","description":"Slash_Shrug_Description","clientOnly":false,"providesPreview":false},{"command":"tableflip","params":"your_message_optional","description":"Slash_Tableflip_Description","clientOnly":false,"providesPreview":false},{"command":"unflip","params":"your_message_optional","description":"Slash_TableUnflip_Description","clientOnly":false,"providesPreview":false},{"command":"create","params":"#channel","description":"Create_A_New_Channel","permission":["create-c","create-p"],"clientOnly":false,"providesPreview":false},{"command":"help","description":"Show_the_keyboard_shortcut_list","clientOnly":false,"providesPreview":false},{"command":"hide","params":"#room","description":"Hide_room","clientOnly":false,"providesPreview":false},{"command":"invite","params":"@username","description":"Invite_user_to_join_channel","permission":"add-user-to-joined-room","clientOnly":false,"providesPreview":false},{"command":"invite-all-to","params":"#room","description":"Invite_user_to_join_channel_all_to","permission":["add-user-to-joined-room","add-user-to-any-c-room","add-user-to-any-p-room"],"clientOnly":false,"providesPreview":false},{"command":"invite-all-from","params":"#room","description":"Invite_user_to_join_channel_all_from","permission":"add-user-to-joined-room","clientOnly":false,"providesPreview":false},{"command":"join","params":"#channel","description":"Join_the_given_channel","permission":"view-c-room","clientOnly":false,"providesPreview":false},{"command":"kick","params":"@username","description":"Remove_someone_from_room","permission":"remove-user","clientOnly":false,"providesPreview":false},{"command":"leave","description":"Leave_the_current_channel","permission":["leave-c","leave-p"],"clientOnly":false,"providesPreview":false},{"command":"part","description":"Leave_the_current_channel","permission":["leave-c","leave-p"],"clientOnly":false,"providesPreview":false},{"command":"me","params":"your_message","description":"Displays_action_text","clientOnly":false,"providesPreview":false},{"command":"msg","params":"@username <message>","description":"Direct_message_someone","permission":"create-d","clientOnly":false,"providesPreview":false},{"command":"mute","params":"@username","description":"Mute_someone_in_room","permission":"mute-user","clientOnly":false,"providesPreview":false},{"command":"unmute","params":"@username","description":"Unmute_someone_in_room","permission":"mute-user","clientOnly":false,"providesPreview":false},{"command":"status","params":"Slash_Status_Params","description":"Slash_Status_Description","clientOnly":false,"providesPreview":false},{"command":"topic","params":"Slash_Topic_Params","description":"Slash_Topic_Description","permission":"edit-room","clientOnly":false,"providesPreview":false},{"command":"unarchive","params":"#channel","description":"Unarchive","permission":"unarchive-room","clientOnly":false,"providesPreview":false}],"offset":0,"count":24,"total":24,"success":true}';
    }

    public function rolesList()
    {
        return '{"roles":[{"_id":"admin","description":"Admin","mandatory2fa":false,"name":"admin","protected":true,"scope":"Users"},{"_id":"moderator","description":"Moderator","mandatory2fa":false,"name":"moderator","protected":true,"scope":"Subscriptions"},{"_id":"leader","description":"Leader","mandatory2fa":false,"name":"leader","protected":true,"scope":"Subscriptions"},{"_id":"owner","description":"Owner","mandatory2fa":false,"name":"owner","protected":true,"scope":"Subscriptions"},{"_id":"user","description":"","mandatory2fa":false,"name":"user","protected":true,"scope":"Users"},{"_id":"bot","description":"","mandatory2fa":false,"name":"bot","protected":true,"scope":"Users"},{"_id":"app","description":"","mandatory2fa":false,"name":"app","protected":true,"scope":"Users"},{"_id":"guest","description":"","mandatory2fa":false,"name":"guest","protected":true,"scope":"Users"},{"_id":"anonymous","description":"","mandatory2fa":false,"name":"anonymous","protected":true,"scope":"Users"},{"_id":"livechat-agent","description":"Livechat Agent","mandatory2fa":false,"name":"livechat-agent","protected":true,"scope":"Users"},{"_id":"livechat-manager","description":"Livechat Manager","mandatory2fa":false,"name":"livechat-manager","protected":true,"scope":"Users"}],"success":true}';
    }

    public function emojiCustomList()
    {
        return '{"emojis":{"update":[],"remove":[]},"success":true}';
    }
}
