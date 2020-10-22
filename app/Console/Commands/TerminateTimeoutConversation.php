<?php

namespace App\Console\Commands;

use App\Models\Conversation;
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
     * @return int
     */
    public function handle(Conversation $query)
    {
        // DB::listen(fn(\Illuminate\Database\Events\QueryExecuted $q) => dump($q->sql));
        /**
         * @var Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
         */
        $query->where('status', Conversation::STATUS_OPEN)->whereHas('lastMessage', function ($query) {
            /**
             * @var Message|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
             */
            return $query->whereHas('institution', function ($query) {
                /**
                 * @var Institution|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
                 */
                return $query->whereRaw(DB::raw('unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(messages.updated_at)>institutions.timeout'));
            });
        })->chunk(50, function (Collection $conversations) {
            $conversations->each(function (Conversation $conversation) {
                $conversationRepository = app(ConversationRepository::class);
                $conversationRepository->terminateTimeout($conversation);
            });
        });

        return 0;
    }
}
