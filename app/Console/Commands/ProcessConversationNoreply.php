<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\Institution;
use App\Models\Message;
use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class ProcessConversationNoreply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversation:noreply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @param Conversation|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @param Institution $institution
     * @return int
     */
    public function handle(Conversation $query, Institution $institution)
    {
        $manager = Permission::findByName('manager', 'api');

        /**
         * @var \Eloquent|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|Conversation $query
         */
        $query->where('noreply', Conversation::NOREPLY_STATUS_NO)
            ->whereNotNull('visitor_last_reply_at')
            ->where(function ($query) {
                /**
                 * @var \Eloquent|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|Conversation $query
                 */
                return $query->where(function ($query) {
                    /**
                     * @var \Eloquent|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|Conversation $query
                     */
                    return $query->whereNotNull('user_last_reply_at')
                    ->where(DB::raw('visitor_last_reply_at>user_last_reply_at'));
                })->where(function ($query) {
                    /**
                     * @var \Eloquent|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|Conversation $query
                     */
                    return $query->whereNull('user_last_reply_at');
                });
            })
            ->leftJoin($institution->getTable(), $institution->getTable() . '.id', '=', $query->getTable() . '.institution_id')
            ->where(DB::raw('unix_timestamp(CURRENT_TIMESTAMP)-unix_timestamp(visitor_last_reply_at)>' . $institution->getTable() . '.noreply_timeout'))
            ->select($query->getTable() . '.*')
            ->chunk(50, function (Collection $conversations) use ($manager) {
                $conversations->each(function (Conversation $conversation) use ($manager) {
                    $user = $conversation->user;
                    if (!$user) {
                        if ($conversation->institution->users->count()) {
                            $user = $conversation->institution->users->random();
                        } else {
                            $user = $conversation->institution->enterprise->users()->permissions($manager)->first();
                        }
                    }
                    /**
                     * @var MessageRepository $messageRepository
                     */
                    $messageRepository = app(MessageRepository::class);
                    $messageRepository->sendMessage($conversation, $user, true, false, Message::TYPE_TEXT, $conversation->institution->noreply);
                });
            });

        return 0;
    }
}
