<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('web_views', function (Blueprint $table) {
            $table->id ();
            $table->uuid('web_user')->default ('')->comment ('用户标识');
            $table->string ('view_url', 200)->default ('')->comment ('访问链接地址');
            $table->ipAddress ('client_ip')->default ('')->comment ('用户IP地址');
            $table->string ('user_agent', 250)->default ('')->comment ('浏览器标识');
            $table->timestamp ('view_at')->nullable ()->comment ('浏览时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists ('web_views');
    }
}
