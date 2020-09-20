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
        ]);
    }
}
