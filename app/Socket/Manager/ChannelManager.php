<?php

namespace App\Socket\Manager;

use App\Socket\Channel\PresenceChannel;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\Channel;
use BeyondCode\LaravelWebSockets\WebSockets\Channels\ChannelManagers\ArrayChannelManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Ratchet\ConnectionInterface;

class ChannelManager extends ArrayChannelManager
{
    protected function determineChannelClass(string $channelName): string
    {
        if (Str::startsWith($channelName, 'presence-')) {
            return PresenceChannel::class;
        }

        return parent::determineChannelClass($channelName);
    }

    /**
     * @param \BeyondCode\LaravelWebSockets\Server\Logger\ConnectionLogger $connection
     */
    public function removeFromAllChannels(ConnectionInterface $connection)
    {
        if (! isset($connection->app)) {
            return;
        }

        /*
         * Remove the connection from all channels.
         */
        /**
         * @var Channel|PrivateChannel|PresenceChannel $each
         */
        $each = collect(Arr::get($this->channels, $connection->app->id, []))->each;

        collect(Arr::get($this->channels, $connection->app->id, []))->each(function ($channel) use ($connection) {
            /**
             * @var PrivateChannel|PresenceChannel $channel
             */
            if (Arr::get($channel->getSubscribedConnections(), $connection->socketId)) {
                Http::post(route('broadcasting.conversation.leavel'), [
                    'channel_name' => $channel->getName(),
                    'member' => Arr::get($channel->getUsers(), Arr::get($channel->getSockets(), $connection->socketId)),
                ]);
            }
        });
        $each->unsubscribe($connection);

        /*
         * Unset all channels that have no connections so we don't leak memory.
         */
        collect(Arr::get($this->channels, $connection->app->id, []))
            ->reject->hasConnections()
                    ->each(function (Channel $channel, string $channelName) use ($connection) {
                        unset($this->channels[$connection->app->id][$channelName]);
                    });

        if (count(Arr::get($this->channels, $connection->app->id, [])) === 0) {
            unset($this->channels[$connection->app->id]);
        }
    }
}
