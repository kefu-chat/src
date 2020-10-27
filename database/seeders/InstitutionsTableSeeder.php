<?php

namespace Database\Seeders;

use App\Models\Enterprise;
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
        if (!Enterprise::latest('id')->first()) {
            $this->call(EnterprisesTableSeeder::class);
        }
        $enterprise = Enterprise::latest('id')->first();
        $institution = new Institution([
            'name' => $generator->company,
            'website' => 'https://' . $generator->domainName,
        ]);
        $institution->enterprise()->associate($enterprise);
        $institution->save();
    }
}
