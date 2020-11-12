<?php

namespace App\Socket\Handler;

use ArrayObject;
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
        payload_route:
        switch (data_get($payload, 'msg')) {
            case 'connect':
                $connection->send(json_encode(['msg' => 'connected', 'server_id' => '0', 'session' => Str::random(),]));
                break;

            case 'ping':
                $connection->send(json_encode(['msg' => 'pong']));
                break;

            case 'sub':
                break;

            case 'login':
                $connection->send(json_encode(['msg' => 'result', 'result' => []]));
                break;

            case 'method':
                $params = data_get($payload, 'params');
                $payload = new ArrayObject([
                    'msg' => data_get($payload, 'method'),
                ] + $params);
                goto payload_route;
                break;
        }
    }
}
