<?php

namespace Database\Seeders;

use App\Models\Plan;
use Faker\Generator;
use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
        Plan::firstOrCreate([
            'name' => '基础版',
        ], [
            'price_monthly' => 0,
            'price_annually' => 0,
            'price_biennially' => 0,
            'price_triennially' => 0,
            'available' => 1,
            'desc' => $generator->paragraph,
            'concurrent' => 2,
            'seats' => 2,
            'sites' => 1,
            'statistics' => false,
            'invite' => 0,
            'theme' => '5',
            'archive_days' => '90',
            'support_wechat' => false,
            'support_phone' => false,
            'desensitize' => false,
            'remove_powered_by' => false,
            'private_deploy' => false,
            'sso' => false,
        ]);
        Plan::firstOrCreate([
            'name' => '专业版',
        ], [
            'price_monthly' => 100,
            'price_annually' => 800,
            'price_biennially' => 1500,
            'price_triennially' => 2250,
            'available' => 1,
            'desc' => $generator->paragraph,
            'concurrent' => 10,
            'seats' => 5,
            'sites' => 1,
            'statistics' => true,
            'invite' => 50,
            'theme' => '10',
            'archive_days' => '365',
            'support_wechat' => true,
            'support_phone' => false,
            'desensitize' => false,
            'remove_powered_by' => true,
            'private_deploy' => false,
            'sso' => false,
        ]);
        Plan::firstOrCreate([
            'name' => '企业版',
        ], [
            'price_monthly' => 500,
            'price_annually' => 2800,
            'price_biennially' => 5200,
            'price_triennially' => 7800,
            'available' => 1,
            'desc' => $generator->paragraph,
            'concurrent' => 100,
            'seats' => 50,
            'sites' => 5,
            'statistics' => true,
            'invite' => 999,
            'theme' => '99',
            'archive_days' => '731',
            'support_wechat' => true,
            'support_phone' => true,
            'desensitize' => true,
            'remove_powered_by' => true,
            'private_deploy' => false,
            'sso' => true,
        ]);
    }
}
