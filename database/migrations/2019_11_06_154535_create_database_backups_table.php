<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateDatabaseBackupsTable.
 */
class CreateDatabaseBackupsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('database_backups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->default ('')->comment('备份名称');
            $table->string('path_file', 150)->default ('')->comment('备份文件路径');
            $table->timestamp('start_at')->nullable()->comment('开始备份时间');
            $table->timestamp('end_at')->nullable()->comment('结束备份时间');
            $table->unsignedBigInteger('file_size')->default (0)->comment('数据压缩大小');
            $table->tinyInteger('status')->default (0)->comment('状态');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::drop('database_backups');
    }
}
