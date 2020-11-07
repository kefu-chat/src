<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Redis;

class VisitorTimeout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'visitor:timeout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将超时的访客踢下线';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(Conversation $query)
    {
        // 精确的将 laravel-echo-server 中还在线的会话之外的会话, 踢下线
        // mget presence-conversation.RgX5d1u6KY2zw3jK:members
        /**
         * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
         */
        $query->with(['visitor',])->where('online_status', true)->chunk(100, function (Collection $list) {
            $channels = $list->pluck('public_id')->map(fn($id) => 'presence-conversation.' . $id . ':members');
            // TODO: 重写校验离线的逻辑
            $echo_list = collect(Redis::connection('echo')->mget($channels->toArray()));
            $no_members_offline = $echo_list->filter(fn($res) => !$res || strlen($res) < 10);
            $has_members_offline = $echo_list->filter(fn($res) => $res && strlen($res) > 10 && count(collect(json_decode($res))->where('user_info.user_type_text', 'visitor')));
            $offlines = $no_members_offline->keys()->merge($has_members_offline->keys());
            $offlines->each(function ($key) use ($list) {
                $conversation = $list[$key];
                $conversation->offline($conversation->visitor);
            });

            return true;
        });
        return 0;
    }
}
