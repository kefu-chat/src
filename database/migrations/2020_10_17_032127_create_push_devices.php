<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePushDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('push_devices', function (Blueprint $table) {
            $table->id();
            $table->string('user_type', 90)->index()->comment('用户表类型');
            $table->unsignedBigInteger('user_id')->index()->comment('用户ID');
            $table->string('user_agent')->nullable()->comment('设备类型: 操作系统+浏览器+版本');
            $table->string('ip', 80)->nullable()->comment('设备IP地址');
            $table->string('fingerprint', 16)->unique()->comment('指纹');
            $table->text('subscription')->comment('订阅详情，包含endpoint expirationTime keys.auth keys.p256dh');
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
        Schema::dropIfExists('push_devices');
    }
}
