<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('pages', function (Blueprint $table) {
            $table->id ();
            $table->foreignId ('user_id');
            $table->string ('name', 100)->default ('')->comment ('英文别名')->unique ();
            $table->string ('title', 200)->default ('')->comment ('标题名称');
            $table->unsignedBigInteger ('cover_id')->default (0)->comment ('封面图片');
            $table->string ('desc', 200)->default ('')->comment ('摘要描述');
            $table->text ('content')->comment ('内容');
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
        Schema::dropIfExists ('pages');
    }
}
