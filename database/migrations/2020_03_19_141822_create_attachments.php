<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('attachments', function (Blueprint $table) {
            $table->id ();
            $table->unsignedBigInteger ('user_id');
            $table->string ('name')->default ('')->comment ('附件名称');
            $table->string ('path')->default ('')->comment ('附件地址');
            $table->string ('file_md5', 32)->default ('')->comment ('文件MD5');
            $table->string ('file_sha1', 60)->default ('')->comment ('文件SHA1');
            $table->tinyInteger ('status')->default (0)->comment ('');
            $table->morphs ('source');
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
        Schema::dropIfExists ('attachments');
    }
}
