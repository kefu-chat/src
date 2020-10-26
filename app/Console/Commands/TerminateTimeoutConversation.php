<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\Institution;
use App\Repositories\ConversationRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TerminateTimeoutConversation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'terminate:timeout-conversation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '关闭超时的会话';

    /**
     * Execute the console command.
     *
     * @param Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param Institution $institution
     * @return int
     */
    public function handle(Conversation $query, Institution $institution)
    {
        // DB::listen(fn(\Illuminate\Database\Events\QueryExecuted $q) => dump($q->sql));
        /**
         * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
         */
        $query->where('status', Conversation::STATUS_OPEN)
            ->whereNotNull('user_last_reply_at')
            ->whereNotNull('visitor_last_reply_at')
            ->where(DB::raw('visitor_last_reply_at<user_last_reply_at'))
            ->leftJoin($institution->getTable(), $institution->getTable() . '.id', '=', $query->getTable() . '.institution_id')
            ->where(DB::raw('unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(user_last_reply_at)>' . $institution->getTable() . '.timeout'))
            ->select($query->getTable() . '.*')
            ->chunk(50, function (Collection $conversations) {
                $conversations->each(function (Conversation $conversation) {
                    $conversationRepository = app(ConversationRepository::class);
                    $conversationRepository->terminateTimeout($conversation);
                });
            });

        return 0;
    }
}
