<?php

namespace App\Socket\Channel;

use BeyondCode\LaravelWebSockets\WebSockets\Channels\PresenceChannel as BasePresenceChannel;

class PresenceChannel extends BasePresenceChannel
{
    public function getSockets(): array
    {
        return $this->sockets;
    }
}
