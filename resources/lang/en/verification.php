<?php

use App\Notifications\VerifyEmail;

return [
    'invalid' => 'Verification link invalid!',
    'link_expired' => 'Verification link expired! Please re-send link and confirm within ' . (VerifyEmail::EXPIRES_TTL / 60) . ' hours!',
];
