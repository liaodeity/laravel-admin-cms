<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAdminsTable.
 */
class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('admins', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string ('username', 50)->default ('')->comment ('登录名称');
            $table->string ('password', 128)->default ('');
            $table->string ('nickname', 20)->default ('')->comment ('昵称');
            $table->string ('phone', 20)->default ('')->comment ('联系电话');
            $table->tinyInteger('send_order_tips')->default(0)->comment('是否接受订单推送提醒');
            $table->tinyInteger ('status')->default (0)->comment ('状态');
            $table->unsignedBigInteger ('admin_id')->default (0)->comment ('创建人');
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
        Schema::drop ('admins');
    }
}
