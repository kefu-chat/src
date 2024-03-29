<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\Institution;
use App\Models\User;
use App\Models\UserSocialite;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

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
        $enterprise = Enterprise::latest('id')->first();
        if (!$enterprise) {
            $this->call(EnterprisesTableSeeder::class);
            goto begin;
        }
        $permission = Permission::findOrCreate('support', 'api');
        if (!$permission) {
            $this->call(PermissionSeeder::class);
            goto begin;
        }

        $name = $generator->name;
        $email = $generator->companyEmail;
        $user = new User([
            'name' => $name,
            //'email' => $email,
            'title' => $generator->jobTitle,
            'password' => '$2y$10$KvdJSsvIZb7B53GP/h5NFuPDtNJLRwgXB75kYT7ueYI6bWdNNwPym', //password_hash('123456', 1),
        ]);
        $user->institution()->associate($institution);
        $user->enterprise()->associate($enterprise);
        $user->save();
        $user->givePermissionTo($permission);

        $userSocialite = new UserSocialite();
        $userSocialite->fill([
            'type' => UserSocialite::TYPE_EMAIL,
            'account' => $email,
            'verified_at' => now(),
        ]);
        $userSocialite->user()->associate($user);
        $userSocialite->save();
        $user->markEmailAsVerified();

        echo $email . PHP_EOL;
        echo '123456' . PHP_EOL;
    }
}
