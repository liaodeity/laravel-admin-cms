<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLogsTable.
 */
class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up ()
    {
        Schema::create ('logs', function (Blueprint $table) {
            $table->id ();
            $table->tinyInteger ('type')->default (0)->comment ('日志类型[1=登录,2=添加,3=修改,4=删除,5=查看,6=信息,7=异常,8=待办]');
            $table->string ('title', 50)->default ('')->comment ('标题');
            $table->longText ('content')->comment ('日志内容');
            $table->unsignedBigInteger('user_id')->nullable ()->comment ('记录人');
            $table->morphs ('source');
            $table->timestamps ();
        });
        Schema::create ('log_reads', function (Blueprint $table) {
            $table->id ();
            $table->foreignId('log_id')->constrained ()->cascadeOnDelete ();
            $table->unsignedBigInteger('user_id')->nullable ()->comment ('记录人');
            $table->tinyInteger ('is_read')->default (0)->comment ('是否已读[1=是,0=否]');
            $table->timestamp ('read_at')->nullable ()->comment ('已读时间');
            $table->timestamps ();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down ()
    {
        Schema::drop ('log_reads');
        Schema::drop ('logs');
    }
}
