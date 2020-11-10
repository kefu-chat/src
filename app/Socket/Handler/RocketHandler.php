<?php

namespace App\Socket\Handler;

use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\WebSockets\WebSocketHandler;
use Illuminate\Support\Str;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;

class RocketHandler extends WebSocketHandler
{
    public function onOpen(ConnectionInterface $connection)
    {
        $connection->app = App::findByKey('websocket');
        $this->generateSocketId($connection);
    }

    public function onError(ConnectionInterface $connection, \Exception $e)
    {
        // TODO: Implement onError() method.
        dump(get_class($connection));
        dump($e->getMessage());
        dump($e->getFile());
        dump($e->getLine());
        dump($e->getTraceAsString());
    }

    public function onMessage(ConnectionInterface $connection, MessageInterface $msg)
    {
        /**
         * @var \App\Socket\Message\IncommingMessage $payload
         */
        $payload = json_decode($msg->getPayload());
        switch(data_get($payload, 'msg')) {
            case 'connect':
                $connection->send(['msg' => 'connected', 'server_id' => '0', 'session' => Str::random(),]);
                break;

            case 'ping':
                $connection->send(['msg' => 'pong']);
                break;

            case 'sub':
                break;

            case 'method':
                switch(data_get($payload, 'method')) {
                    case 'connect':
                        break;

                    case 'login':
                        $connection->send(['msg' => 'result', 'id' => $payload->id, 'result' => []]);
                        break;
                }

                break;

        }
    }
}
