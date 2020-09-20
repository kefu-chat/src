<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoupons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('enterprise_id')->nullable()->comment('适用企业,为 null 表示全部');
            $table->unsignedBigInteger('plan_id')->nullable()->comment('适用产品,为 null 表示全部');
            $table->text('periods')->nullable()->comment('适用期限, casts array, 为空表示全部');
            $table->unsignedInteger('using_limit')->index()->default(999999)->comment('适用次数限制');
            $table->unsignedTinyInteger('type')->comment('类型 1打折 2抵扣');
            $table->decimal('amount')->comment('抵扣金额, 打折比例');

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
        Schema::dropIfExists('coupons');
    }
}
