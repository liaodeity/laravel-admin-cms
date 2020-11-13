<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageNoticesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_notices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('type', 20)->default('')->comment('消息类型');
            $table->string('content', 200)->default('')->comment('消息内容');
            $table->unsignedBigInteger('access_id')->default(0)->comment('来源ID');
            $table->string('access_type', 100)->default('')->comment('来源类型');
//            $table->tinyInteger('is_tips')->default(0)->comment('是否已提醒');
//            $table->timestamp('tips_at')->nullable()->comment('提醒时间');
            $table->timestamps();
        });
        Schema::create('message_notice_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string ('openid', 128)->default ('')->comment ('微信用户的唯一标识');
            $table->unsignedBigInteger('notice_id')->default(0)->comment('消息ID');
            $table->unsignedBigInteger('access_id')->default(0)->comment('来源ID');
            $table->string('access_type', 100)->default('')->comment('来类型');
            $table->tinyInteger('is_tips')->default(0)->comment('是否已提醒');
            $table->timestamp('tips_at')->nullable()->comment('提醒时间');
            $table->timestamps();
            $table->foreign ('notice_id')->references ('id')->on ('message_notices')->onDelete ('cascade');//外键
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_notice_users');
        Schema::dropIfExists('message_notices');
    }
}
