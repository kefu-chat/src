<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->unsignedBigInteger('institution_id')->index()->comment('组织 ID');
            $table->string('sender_type')->index()->comment('发送人类型');
            $table->unsignedBigInteger('sender_id')->index()->comment('发送人 ID');
            $table->unsignedBigInteger('conversation_id')->index()->comment('会话 ID');
            $table->tinyInteger('type')->index()->comment('1文字 2图片');
            $table->text('content')->comment('内容');

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
        Schema::dropIfExists('messages');
    }
}
