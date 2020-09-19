<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\Institution;
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
        $enterprise = new Enterprise([
            'name' => $generator->company,
            'serial' => Str::random(15),
        ]);
        $enterprise->save();
    }
}
