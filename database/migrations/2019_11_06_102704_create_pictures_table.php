<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('pictures', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->string ('path', 200)->default ('')->comment ('路径');
            $table->string ('url', 150)->default ('')->comment ('远程地址');
            $table->string ('title', 50)->default ('')->comment ('名称');
            $table->char ('md5', 32)->default ('')->comment ('MD5值');
            $table->char ('sha1', 42)->default ('')->comment ('SHA1值');
            $table->tinyInteger ('status')->default (0)->comment ('状态');
            $table->unsignedBigInteger ('picture_id')->default (0)->comment ('关联ID');
            $table->string ('picture_type', 100)->default ('')->comment ('关联类型');
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
        Schema::dropIfExists ('pictures');
    }
}
