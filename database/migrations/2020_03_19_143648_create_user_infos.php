<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('user_infos', function (Blueprint $table) {
            $table->id ();
            $table->foreignId ('user_id')->constrained ()->cascadeOnDelete ();
            $table->string ('real_name', 20)->default ('')->comment ('真实姓名');
            $table->tinyInteger ('gender')->default (0)->comment ('性别');
            $table->string ('telephone')->default ('')->comment ('电话');
            $table->string ('address')->default ('')->comment ('地址');
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
        Schema::dropIfExists ('user_infos');
    }
}
