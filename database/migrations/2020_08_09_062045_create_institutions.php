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
