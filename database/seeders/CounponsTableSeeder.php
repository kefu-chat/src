<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Enterprise;
use Faker\Generator;
use Illuminate\Database\Seeder;

class CounponsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Coupon::where(['name' => 'onetime',])->first()) {
            $onetime = new Coupon();
            $onetime->fill([
                'name' => 'onetime',
                'periods' => [],
                'using_limit' => 1,
                'type' => Coupon::TYPE_DECR,
                'amount' => 0.5,
            ]);
            $onetime->save();
        }

        if (!Coupon::where(['name' => 'month',])->first()) {
            $month = new Coupon();
            $month->fill([
                'name' => 'month',
                'periods' => ['monthly'],
                'using_limit' => 1,
                'type' => Coupon::TYPE_DECR,
                'amount' => 0.5,
            ]);
            $month->save();
        }

        if (!Coupon::where(['name' => 'enterprise',])->first()) {
            if (!Enterprise::latest('id')->first()) {
                $this->call(EnterprisesTableSeeder::class);
            }
            $enterprise = Enterprise::latest('id')->first();
            $enterprise_coupon = new Coupon();
            $enterprise_coupon->fill([
                'name' => 'enterprise',
                'periods' => [],
                'using_limit' => 1,
                'type' => Coupon::TYPE_DECR,
                'amount' => 0.5,
            ]);
            $enterprise_coupon->enterprise()->associate($enterprise);
            $enterprise_coupon->save();
        }

        if (!Coupon::where(['name' => 'plan',])->first()) {
            if (!Plan::latest('id')->first()) {
                $this->call(PlansTableSeeder::class);
            }
            $plan = Plan::latest('id')->first();
            $plan_coupon = new Coupon();
            $plan_coupon->fill([
                'name' => 'plan',
                'periods' => [],
                'using_limit' => 1,
                'type' => Coupon::TYPE_DECR,
                'amount' => 0.5,
            ]);
            $plan_coupon->plan()->associate($plan);
            $plan_coupon->save();
        }
    }
}
