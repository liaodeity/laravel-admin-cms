<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateRegionsTable.
 */
class CreateRegionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pid')->default(0);
            $table->tinyInteger('level')->default(0);
            $table->string('name', 50)->default('')->comment('区域名称');
            $table->string('area_region', 100)->index()->comment('区域编号链接');
            $table->string('area_region_name', 100)->default('')->comment('区域编号链接名称');
            $table->unsignedBigInteger('province_id')->default(0)->comment('省份');
            $table->unsignedBigInteger('city_id')->default(0)->comment('城市');
            $table->unsignedBigInteger('county_id')->default(0)->comment('县区');
            $table->unsignedBigInteger('town_id')->default(0)->comment('镇区');
            $table->unsignedBigInteger('community_id')->default(0)->comment('社区');
            $table->string('lat', 20)->default('')->comment('纬度');
            $table->string('lng', 20)->default('')->comment('经度');
            $table->tinyInteger('status')->default(0)->comment('状态');
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
        Schema::drop('regions');
    }
}
