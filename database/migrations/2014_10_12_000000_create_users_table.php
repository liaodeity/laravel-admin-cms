<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('users', function (Blueprint $table) {
            $table->id ();
            $table->string ('name')->nullable ()->unique ()->comment ('用户名');
            $table->string ('email')->nullable ()->unique ()->comment ('邮箱');
            $table->string ('account')->nullable ()->unique ()->comment ('登录账号');
            $table->timestamp ('email_verified_at')->nullable ();
            $table->string ('password');
            $table->rememberToken ();
            $table->unsignedBigInteger ('login_count')->default (0)->comment ('登录次数');
            $table->timestamp ('last_login_at')->nullable ()->comment ('最后登录时间');
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
        Schema::dropIfExists ('users');
    }
}
