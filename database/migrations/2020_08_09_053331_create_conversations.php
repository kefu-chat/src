<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConversations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('institution_id')->index()->comment('组织 ID');
            $table->unsignedBigInteger('visitor_id')->index()->comment('访客 ID');
            $table->unsignedBigInteger('user_id')->index()->comment('客服 ID');
            $table->string('ip', 64)->index()->comment('访客 IP');
            $table->string('url')->nullable()->comment('从那个页面来');
            $table->tinyInteger('online_status')->default(1)->comment('会话在线状态');
            $table->timestamp('first_reply_at')->nullable()->comment('初次回复时间');
            $table->timestamp('last_reply_at')->nullable()->comment('上次回复时间');
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
        Schema::dropIfExists('conversations');
    }
}
