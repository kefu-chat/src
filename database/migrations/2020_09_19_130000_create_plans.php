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
            $table->tinyInteger('available')->index()->default(0)->comment('是否开放购买');

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
