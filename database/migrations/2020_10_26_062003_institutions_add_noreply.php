<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InstitutionsAddNoreply extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('noreply')->nullable()->default('我们客服目前无法回复, 请留下您的 QQ、微信、E-mail 或者 手机联系方式, 我们会第一时间给您回复');
            $table->enum('noreply_timeout', [null, 60, 120, 180, 300])->nullable()->default(null)->comment('超时时间');
        });
        Schema::table('conversations', function (Blueprint $table) {
            $table->enum('noreply_status', [0, 1])->nullable()->default(0)->comment('是否已经无人接待处理过了');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn('noreply');
            $table->dropColumn('noreply_timeout');
        });
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('noreply_status');
        });
    }
}
