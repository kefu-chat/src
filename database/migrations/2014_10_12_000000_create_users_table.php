<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index()->comment('网站 ID');
            $table->unsignedBigInteger('enterprise_id')->index()->comment('企业 ID');

            $table->string('name');
            $table->string('avatar')->default(asset('img/default_avatar.svg'))->comment('头像');
            $table->string('email', 64)->unique()->comment('电子邮箱');
            $table->string('title')->nullable()->comment('职位');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
