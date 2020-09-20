<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('enterprise_id');
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('user_id');
            $table->string('period')->comment('期限, monthly annually biennially triennially');
            $table->string('coupon', 16)->nullable()->comment('优惠券');
            $table->decimal('price', 10, 2)->comment('价格');
            $table->decimal('need_pay_price', 10, 2)->comment('实际应支付价格');
            $table->decimal('paid_price', 10, 2)->default(0)->comment('实际已支付价格');
            $table->tinyInteger('status')->comment('订单状态 0待支付 1已支付 -1已取消');

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
        Schema::dropIfExists('orders');
    }
}
