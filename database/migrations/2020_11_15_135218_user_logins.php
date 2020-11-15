<?php

use App\Models\User;
use App\Models\UserSocialite;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserLogins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_socialites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('用户 ID');
            $table->string('type', 16)->comment('登录方式');
            $table->string('account', 120)->comment('账号');
            $table->unique(['user_id', 'type', 'account']);

            $table->timestamp('verified_at')->nullable()->default(null)->comment('验证时间');
            $table->timestamps();
        });

        User::chunk(100, fn (Collection $users) => $users->each(function (User $user) {
            $login = new UserSocialite();
            $login->fill([
                'type' => UserSocialite::TYPE_EMAIL,
                'account' => $user->email,
            ]);
            if ($user->email_verified_at) {
                $login->fill([
                    'verified_at' => now(),
                ]);
            }
            $login->user()->associate($user);
            $login->save();
        }));

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at',]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 64)->nullable();
            $table->timestamp('email_verified_at')->nullable();
        });

        User::with(['userSocialites'])->chunk(100, fn (Collection $users) => $users->each(function (User $user) {
            $login = $user->userSocialites->where('type', UserSocialite::TYPE_EMAIL)->first();
            if ($login->verified_at) {
                $user->email_verified_at = $login->verified_at;
            }
            $user->email = $login->account;
            $user->save();
        }));

        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 64)->unique()->comment('电子邮箱')->change();
        });


        Schema::dropIfExists('user_socialites');
    }
}
