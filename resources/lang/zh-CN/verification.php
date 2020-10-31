<?php

use App\Notifications\VerifyEmail;

return [
    'invalid' => '验证失败!',
    'link_expired' => '链接失效! 请尝试登陆重新获取并在' . (VerifyEmail::EXPIRES_TTL / 60) . '小时内验证!',
];
