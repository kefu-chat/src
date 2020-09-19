<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index()->comment('组织 ID');
            $table->string('unique_id', 32)->unique()->comment('唯一 ID');
            $table->string('name', 120)->index()->comment('名字');
            $table->string('email', 120)->index()->comment('电子邮件');
            $table->string('phone', 15)->index()->comment('手机号');
            $table->string('avatar', 128)->nullable()->comment('头像');
            $table->string('memo')->nullable()->comment('备注');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visitors');
    }
}
