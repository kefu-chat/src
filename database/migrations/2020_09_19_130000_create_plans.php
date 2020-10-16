<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('套餐名字');
            $table->decimal('price_monthly')->comment('月付价格');
            $table->decimal('price_annually')->comment('年付价格');
            $table->decimal('price_biennially')->comment('两年付价格');
            $table->decimal('price_triennially')->comment('三年付价格');
            $table->integer('seats')->default(1)->comment('坐席数');
            $table->integer('concurrent')->default(1)->comment('同时对话数');
            $table->integer('sites')->default(1)->comment('站点数');
            $table->tinyInteger('statistics')->default(0)->comment('统计报表');
            $table->enum('theme', [5, 10, 99,])->default(5)->comment('主题');
            $table->enum('archive_days', [90, 180, 365, 731])->default(90)->comment('对话存档时间');
            $table->integer('invite')->default(0)->comment('每天可主动邀请次数');
            $table->tinyInteger('remove_powered_by')->default(0)->comment('移除版权');
            $table->tinyInteger('support_wechat')->default(0)->comment('1对1 微信QQ支持');
            $table->tinyInteger('support_phone')->default(0)->comment('1对1 电话支持');
            $table->tinyInteger('desensitize')->default(0)->comment('用户资料脱敏');
            $table->tinyInteger('sso')->default(0)->comment('对接企业统一登录');
            $table->tinyInteger('private_deploy')->default(0)->comment('私有部署');
            $table->tinyInteger('available')->index()->default(0)->comment('是否开放购买');
            $table->text('desc')->nullable()->comment('描述');

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
        Schema::dropIfExists('plans');
    }
}
