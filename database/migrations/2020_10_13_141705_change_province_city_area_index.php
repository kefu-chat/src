<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeProvinceCityAreaIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province_city_area', function (Blueprint $table) {
            $table->index('type');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('province_city_area', function (Blueprint $table) {
            $table->dropIndex('province_city_area_type_index');
            $table->dropIndex('province_city_area_parent_id_index');
        });
    }
}
