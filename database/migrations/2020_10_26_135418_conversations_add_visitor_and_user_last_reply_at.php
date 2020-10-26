<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConversationsAddVisitorAndUserLastReplyAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->timestamp('visitor_last_reply_at')->nullable()->default(null)->comment('访客最后回复时间');
            $table->timestamp('user_last_reply_at')->nullable()->default(null)->comment('客服最后回复时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('visitor_last_reply_at');
            $table->dropColumn('user_last_reply_at');
        });
    }
}
