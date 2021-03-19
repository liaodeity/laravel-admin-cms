<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId ('user_id')->constrained ()->cascadeOnDelete ();
            $table->unsignedBigInteger ('login_count')->default (0)->comment ('登录次数');
            $table->timestamp ('last_login_at')->nullable ()->comment ('最后登录时间');
            $table->tinyInteger ('status')->default (0)->comment ('状态');
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
        Schema::dropIfExists('user_admins');
    }
}
