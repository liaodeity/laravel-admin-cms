<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger ('pid')->default (0);
            $table->uuid ('uuid')->unique ()->comment ('菜单唯一性');
            $table->tinyInteger ('type')->default (0)->comment ('类型【1=菜单、2=按钮】');
            $table->string ('cate_module', 20)->default ('')->comment ('分类模型');
            $table->string ('auth_name')->default ('')->comment ('权限名称');
            $table->string ('title')->default ('')->comment ('菜单标题');
            $table->string ('href')->default ('')->comment ('链接地址');
            $table->string ('icon')->default ('')->comment ('图标');
            $table->string ('target')->default ('')->comment ('跳转方式');
            $table->tinyInteger ('is_shortcut')->default (0)->comment ('是否快捷');
            $table->tinyInteger ('status')->default (0)->comment ('状态【1=正常、4=隐藏】');
            $table->smallInteger ('sort')->default (1)->comment ('');
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
        Schema::dropIfExists('menus');
    }
}
