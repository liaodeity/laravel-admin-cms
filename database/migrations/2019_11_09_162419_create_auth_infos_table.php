<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAgentsTable.
 */
class CreateAuthInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('auth_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger ('role_id')->default (0);
            $table->string ('name', 50)->default ('')->comment ('角色名称');
            $table->text ('desc')->comment ('角色说明');
            $table->text('role_value')->comment('权限ID');
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
        Schema::drop ('auth_infos');
    }
}
