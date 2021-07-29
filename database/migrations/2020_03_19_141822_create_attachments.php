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
            $table->string ('path')->default ('')->comment ('开放附件地址');
            $table->string ('storage_path')->default ('')->comment ('私有附件地址');
            $table->string ('file_md5', 32)->default ('')->comment ('文件MD5');
            $table->string ('file_sha1', 60)->default ('')->comment ('文件SHA1');
            $table->string ('url')->default ('')->comment ('云地址');
            $table->decimal ('file_size',12,2)->default (0)->comment ('文件大小（KB）');
            $table->string ('driver', 10)->default ('')->comment ('类型');
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
