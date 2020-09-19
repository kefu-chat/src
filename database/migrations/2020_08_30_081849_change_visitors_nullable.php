<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVisitorsNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('name', 120)->nullable()->comment('名字')->change();
            $table->string('email', 120)->nullable()->comment('电子邮件')->change();
            $table->string('phone', 15)->nullable()->comment('手机号')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->string('name', 120)->comment('名字')->change();
            $table->string('email', 120)->comment('电子邮件')->change();
            $table->string('phone', 15)->comment('手机号')->change();
        });
    }
}
