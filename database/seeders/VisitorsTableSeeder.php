<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\Visitor;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VisitorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
        begin:
        $institution = Institution::latest('id')->first();
        if (!$institution) {
            $this->call(InstitutionsTableSeeder::class);
            goto begin;
        }

        $name = $generator->word;
        $email = $generator->email;
        $visitor = new Visitor([
            'unique_id' => Str::random(),
            'name' => $name,
            'email' => $email,
            'phone' => $generator->phoneNumber,
            'avatar' => null,
            'memo' => $generator->paragraph(mt_rand(1,6)),
        ]);
        $visitor->institution()->associate($institution);
        $visitor->save();

        echo $name . PHP_EOL;
    }
}
