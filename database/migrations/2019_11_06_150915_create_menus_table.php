<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMenusTable.
 */
class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('menus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger ('pid')->default (0)->comment ('父ID');
            $table->string ('auth_name', 100)->default ('')->comment ('权限名称');
            $table->char ('module', 20)->default ('')->comment ('所属模块');
            $table->char ('type', 5)->default ('')->comment ('类型');
            $table->integer ('sort')->default (0)->comment ('排序');
            $table->string ('route_url', 100)->default ('')->comment ('路由地址');
            $table->string ('title', 50)->default ('')->comment ('菜单名称');
            $table->string ('icon', 100)->default ('')->comment ('图标');
            $table->tinyInteger ('status')->default (0)->comment ('状态');
            $table->timestamps ();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::drop ('menus');
    }
}
