<?php

namespace App\Console\Commands;

use App\Models\Conversation;
use App\Models\Visitor;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class ClearEmptyVisitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:empty-visitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清理闲置超过 90 天从来没聊过的访客';

    /**
     * 删除多少天以前的空数据
     *
     * @var integer
     */
    protected $days = 90;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * @var \Eloquent|Visitor $query
         */
        $query = app(Visitor::class);
        $query->where('created_at', '<', now()->subDays($this->days))->whereHas('conversations')->whereDoesntHave('conversations.messages')->chunk(50, function (Collection $visitors) {
            $visitors->each(function (Visitor $visitor) {
                $visitor->conversations->each(function (Conversation $conversation) {
                    $conversation->delete();
                });
                $visitor->delete();
            });
        });
        return 0;
    }
}
