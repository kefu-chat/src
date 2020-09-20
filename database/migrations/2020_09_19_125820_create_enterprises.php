<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterprises extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enterprises', function (Blueprint $table) {
            $table->id();

            $table->string('name')->nullable()->comment('企业名称');
            $table->string('serial')->nullable()->comment('公司注册号');
            $table->unsignedBigInteger('plan_id')->default(1)->comment('套餐 ID');

            $table->timestamps();
            $table->timestamp('plan_expires_at')->default('2038-01-01')->comment('套餐过期时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enterprises');
    }
}
