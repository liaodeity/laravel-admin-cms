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
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string ('type', 50)->default ('')->comment ('日志类型');
            $table->text ('content')->comment ('日志内容');
            $table->unsignedBigInteger ('admin_id')->default (0);
            $table->unsignedBigInteger ('agent_id')->default (0);
            $table->unsignedBigInteger ('member_id')->default (0);
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
        Schema::drop ('logs');
    }
}
