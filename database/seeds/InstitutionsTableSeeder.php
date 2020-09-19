<?php

use App\Models\Institution;
use Faker\Generator;
use Illuminate\Database\Seeder;

class InstitutionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $generator)
    {
      $institution = new Institution([
          'name' => $generator->company,
          'serial' => $generator->creditCardNumber,
      ]);
      $institution->save();
    }
}
