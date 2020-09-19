<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
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

        $name = $generator->name;
        $email = $generator->companyEmail;
        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => '$2y$10$KvdJSsvIZb7B53GP/h5NFuPDtNJLRwgXB75kYT7ueYI6bWdNNwPym', //password_hash('123456', 1),
        ]);
        $user->institution()->associate($institution);
        $user->save();

        echo $email . PHP_EOL;
        echo '123456' . PHP_EOL;
    }
}
