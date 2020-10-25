<?php

namespace App\Console\Commands;

use App\Models\Enterprise;
use App\Models\Plan;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

class CancelSubscriptionExpiredEnterprise extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cancel:subscription-expired-enterprise';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '取消已经过期的付费企业的套餐';

    protected $plan_id = 1;

    protected $plan_expires_at = '2038-01-01';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $plan = Plan::findOrFail($this->plan_id);

        /**
         * @var \Eloquent|Enterprise $query
         */
        $query = app(Enterprise::class);
        $query->where('plan_id', '>', 1)->where('plan_expires_at', '<', now())->chunk(50, function (Collection $enterprises) use ($plan) {
            $enterprises->each(function (Enterprise $enterprise) use ($plan) {
                $enterprise->plan()->associate($plan);
                $enterprise->fill(['plan_expires_at' => $this->plan_expires_at,]);
                $enterprise->save();
                //@TODO: 发送通知
            });
        });
        return 0;
    }
}
