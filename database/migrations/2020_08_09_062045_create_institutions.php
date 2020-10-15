<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable()->comment('项目名字');
            $table->string('website')->nullable()->comment('网站');
            $table->string('technical_name')->nullable()->comment('技术负责人');
            $table->string('billing_name')->nullable()->comment('财务负责人');
            $table->string('technical_phone')->nullable()->comment('技术负责人电话');
            $table->string('billing_phone')->nullable()->comment('财务负责人电话');
            $table->string('terminate_manual')->default('本次对话已结束, 若您需要我们继续服务您可以重新打开咨询')->comment('人工结束对话文案');
            $table->string('terminate_timeout')->default('由于您长时间未回复, 本次对话已结束, 若您需要我们继续服务您可以重新打开咨询')->comment('超时结束对话文案');
            $table->string('greeting_message')->default('您好, 请问有什么可以帮您的?')->comment('打招呼文本');
            $table->enum('theme', [
                'default',
                'blue1',
                'blue2',
                'blue-purple',
                'purple',
                'pink',
                'dark',
                'cyan',
                'red1',
                'red2',
                'red3',
                'green1',
                'green2',
                'green3',
                'blue-light',
            ])->default('default')->comment('网站配色方案');
            $table->unsignedBigInteger('enterprise_id')->comment('企业 ID');

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
        Schema::dropIfExists('institutions');
    }
}
