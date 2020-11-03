<?php
namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends RocketBaseController
{
    public function oauth(Request $request)
    {
        return response()->json([
            'services' => [
                [
                    '_id' => 'XYCwL7CguzzsXWvt5',
                    'name' => 'google',
                    'clientId' => '662030055877-uma7qar29ji84gopa740fasv2f6rfj1v.apps.googleusercontent.com',
                    'buttonLabelText' => '微信登录',
                    'buttonColor' => '',
                    'buttonLabelColor' => '',
                    'custom' => false,
                ],
                [
                    '_id' => 'd9fTPfTrbzztyDEmG',
                    'name' => 'meteor-developer',
                    'clientId' => 'LDGYjcX2knskEP8HR',
                    'buttonLabelText' => '企业微信登录',
                    'buttonColor' => '',
                    'buttonLabelColor' => '',
                    'custom' => false,
                ],
                [
                    '_id' => 'QWmNpMhWvSePfyAYf',
                    'name' => 'twitter',
                    'clientId' => 'njl2I82AXrOo0YftZyKHyC8aV',
                    'buttonLabelText' => '钉钉登录',
                    'buttonColor' => '',
                    'buttonLabelColor' => '',
                    'custom' => false,
                ],
                [
                    '_id' => 'rrwadTxjYkJoxB6Li',
                    'service' => 'alexaskill2020',
                    'accessTokenParam' => 'access_token',
                    'authorizePath' => '/oauth/authorize',
                    'avatarField' => '',
                    'buttonColor' => '#1d74f5',
                    'buttonLabelColor' => '#FFFFFF',
                    'buttonLabelText' => '',
                    'clientId' => '',
                    'custom' => true,
                    'emailField' => '',
                    'identityPath' => '/me',
                    'identityTokenSentVia' => 'default',
                    'loginStyle' => 'popup',
                    'mergeRoles' => false,
                    'mergeUsers' => true,
                    'nameField' => '',
                    'rolesClaim' => 'roles',
                    'scope' => 'openid',
                    'serverURL' => 'https://' . $this->getApiDomain() . '/api/v1',
                    'showButton' => false,
                    'tokenPath' => '/oauth/token',
                    'tokenSentVia' => 'payload',
                    'usernameField' => '',
                ],
            ],
            'success' => true,
        ]);
    }

    public function public(Request $request)
    {
        if (Str::contains($request->input('query'), 'API_Gitlab_URL')) {
            return response()->json([
                'settings' => [
                    0 => [
                        '_id' => 'API_Gitlab_URL',
                        'value' => 'https://gitlab.com',
                        'enterprise' => false,
                    ],
                    1 => [
                        '_id' => 'Accounts_EmailOrUsernamePlaceholder',
                        'value' => '',
                        'enterprise' => false,
                    ],
                    2 => [
                        '_id' => 'Accounts_EmailVerification',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    3 => [
                        '_id' => 'Accounts_Iframe_api_method',
                        'value' => 'POST',
                        'enterprise' => false,
                    ],
                    4 => [
                        '_id' => 'Accounts_Iframe_api_url',
                        'value' => '',
                        'enterprise' => false,
                    ],
                    5 => [
                        '_id' => 'Accounts_ManuallyApproveNewUsers',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    6 => [
                        '_id' => 'Accounts_PasswordPlaceholder',
                        'value' => '',
                        'enterprise' => false,
                    ],
                    7 => [
                        '_id' => 'Accounts_PasswordReset',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    8 => [
                        '_id' => 'Accounts_RegistrationForm',
                        'value' => 'Public',
                        'enterprise' => false,
                    ],
                    9 => [
                        '_id' => 'Accounts_RegistrationForm_LinkReplacementText',
                        'value' => 'Registration temporarily disabled. Please check back later!',
                        'enterprise' => false,
                    ],
                    10 => [
                        '_id' => 'Accounts_ShowFormLogin',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    11 => [
                        '_id' => 'Accounts_iframe_enabled',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    12 => [
                        '_id' => 'CAS_enabled',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    13 => [
                        '_id' => 'CAS_login_url',
                        'value' => '',
                        'enterprise' => false,
                    ],
                    14 => [
                        '_id' => 'Site_Url',
                        'value' => 'https://' . $this->getApiDomain(),
                        'enterprise' => false,
                    ],
                ],
                'count' => 15,
                'offset' => 0,
                'total' => 15,
                'success' => true,
            ]);
        }
        if (Str::contains($request->input('query'), 'Accounts_AllowEmailChange')) {
            return response()->json([
                'settings' => [
                    0 => [
                        '_id' => 'API_Use_REST_For_DDP_Calls',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    1 => [
                        '_id' => 'Accounts_AllowEmailChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    2 => [
                        '_id' => 'Accounts_AllowPasswordChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    3 => [
                        '_id' => 'Accounts_AllowRealNameChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    4 => [
                        '_id' => 'Accounts_AllowUserAvatarChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    5 => [
                        '_id' => 'Accounts_AllowUserProfileChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    6 => [
                        '_id' => 'Accounts_AllowUserStatusMessageChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    7 => [
                        '_id' => 'Accounts_AllowUsernameChange',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    8 => [
                        '_id' => 'Accounts_CustomFields',
                        'value' => '',
                        'enterprise' => false,
                    ],
                    9 => [
                        '_id' => 'Accounts_Directory_DefaultView',
                        'value' => 'channels',
                        'enterprise' => false,
                    ],
                    10 => [
                        '_id' => 'Allow_Save_Media_to_Gallery',
                        'enterprise' => false,
                        'value' => true,
                    ],
                    11 => [
                        '_id' => 'Assets_favicon_512',
                        'value' => [
                            'defaultUrl' => 'rocketchat.png',
                        ],
                        'enterprise' => false,
                    ],
                    12 => [
                        '_id' => 'AutoTranslate_Enabled',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    13 => [
                        '_id' => 'CROWD_Enable',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    14 => [
                        '_id' => 'DirectMesssage_maxUsers',
                        'value' => 8,
                        'enterprise' => false,
                    ],
                    15 => [
                        '_id' => 'E2E_Enable',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    16 => [
                        '_id' => 'FEDERATION_Enabled',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    17 => [
                        '_id' => 'FileUpload_MaxFileSize',
                        'value' => 100857600,
                        'enterprise' => false,
                    ],
                    18 => [
                        '_id' => 'FileUpload_MediaTypeWhiteList',
                        'value' => '',
                        'enterprise' => false,
                    ],
                    19 => [
                        '_id' => 'Force_Screen_Lock',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    20 => [
                        '_id' => 'Force_Screen_Lock_After',
                        'value' => 1800,
                        'enterprise' => false,
                    ],
                    21 => [
                        '_id' => 'Hide_System_Messages',
                        'value' => [],
                        'enterprise' => false,
                    ],
                    22 => [
                        '_id' => 'Jitsi_Domain',
                        'value' => 'jitsi.rocket.chat',
                        'enterprise' => false,
                    ],
                    23 => [
                        '_id' => 'Jitsi_Enabled',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    24 => [
                        '_id' => 'Jitsi_Enabled_TokenAuth',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    25 => [
                        '_id' => 'Jitsi_SSL',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    26 => [
                        '_id' => 'Jitsi_URL_Room_Prefix',
                        'value' => 'RocketChat',
                        'enterprise' => false,
                    ],
                    27 => [
                        '_id' => 'LDAP_Enable',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    28 => [
                        '_id' => 'Livechat_request_comment_when_closing_conversation',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    29 => [
                        '_id' => 'Message_AllowDeleting',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    30 => [
                        '_id' => 'Message_AllowDeleting_BlockDeleteInMinutes',
                        'value' => 0,
                        'enterprise' => false,
                    ],
                    31 => [
                        '_id' => 'Message_AllowEditing',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    32 => [
                        '_id' => 'Message_AllowEditing_BlockEditInMinutes',
                        'value' => 0,
                        'enterprise' => false,
                    ],
                    33 => [
                        '_id' => 'Message_AllowPinning',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    34 => [
                        '_id' => 'Message_AllowStarring',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    35 => [
                        '_id' => 'Message_AudioRecorderEnabled',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    36 => [
                        '_id' => 'Message_GroupingPeriod',
                        'value' => 300,
                        'enterprise' => false,
                    ],
                    37 => [
                        '_id' => 'Message_Read_Receipt_Enabled',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    38 => [
                        '_id' => 'Message_Read_Receipt_Store_Users',
                        'value' => false,
                        'enterprise' => false,
                    ],
                    39 => [
                        '_id' => 'Message_TimeAndDateFormat',
                        'value' => 'LLL',
                        'enterprise' => false,
                    ],
                    40 => [
                        '_id' => 'Message_TimeFormat',
                        'value' => 'LT',
                        'enterprise' => false,
                    ],
                    41 => [
                        '_id' => 'Site_Name',
                        'value' => '客服洽',
                        'enterprise' => false,
                    ],
                    42 => [
                        '_id' => 'Store_Last_Message',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    43 => [
                        '_id' => 'Threads_enabled',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    44 => [
                        '_id' => 'UI_Allow_room_names_with_special_chars',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    45 => [
                        '_id' => 'UI_Use_Real_Name',
                        'value' => true,
                        'enterprise' => false,
                    ],
                    46 => [
                        '_id' => 'uniqueID',
                        'value' => 'eoRXMCHBbQCdDnrke',
                        'enterprise' => false,
                    ],
                ],
                'count' => 47,
                'offset' => 0,
                'total' => 47,
                'success' => true,
            ]);
        }
    }
}
