<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\Institution;
use App\Models\Plan;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EnterprisesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
        if (!Plan::latest('id')->first()) {
            $this->call(PlansTableSeeder::class);
        }
        $plan = Plan::first();
        $enterprise = new Enterprise([
            'name' => $generator->company,
            'serial' => Str::random(15),
            'geographic' => [
                'province' => [
                    'key' => $generator->postcode,
                ],
                'city' => [
                    'key' => $generator->postcode,
                ],
            ],
            'address' => $generator->address,
            'profile' => $generator->sentence,
            'country' => 'CN',
            'phone' => $generator->phoneNumber,
        ]);
        $enterprise->plan()->associate($plan);
        $enterprise->save();
    }
}
