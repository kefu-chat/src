<?php

namespace Database\Seeders;

use App\Models\Enterprise;
use App\Models\Institution;
use App\Models\User;
use App\Models\UserSocialite;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    const ADMIN_EMAIL = 'admin@admin.com';
    const ADMIN_OPENID = 'admin-openid';

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
        $permission = Permission::findOrCreate('manager', 'api');
        if (!$permission) {
            $this->call(PermissionSeeder::class);
            goto begin;
        }

        $user = new User([
            'name' => 'admin',
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
            'account' => self::ADMIN_EMAIL,
            'verified_at' => now(),
        ]);
        $userSocialite->user()->associate($user);
        $userSocialite->save();
        $wxappSocialite = new UserSocialite();
        $wxappSocialite->fill([
            'type' => UserSocialite::TYPE_WXAPP,
            'account' => self::ADMIN_OPENID,
            'verified_at' => now(),
        ]);
        $wxappSocialite->user()->associate($user);
        $wxappSocialite->save();
        $user->markEmailAsVerified();

        echo 'admin@admin.com' . PHP_EOL;
        echo '123456' . PHP_EOL;
    }
}
