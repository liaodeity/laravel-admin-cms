<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxReplyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_reply', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('content')->comment('回复内容');
            $table->tinyInteger('type')->default(1)->comment('类型');
            $table->tinyInteger('if_like')->default(0)->comment('模糊搜索');
            $table->tinyInteger('is_subscribe')->default(0)->comment('是否关注事件');
            $table->tinyInteger('status')->default(0)->comment('状态');
            $table->timestamps();
        });
        Schema::create('wx_reply_keywords', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reply_id')->default(0);
            $table->string('keyword', 100)->default('')->index()->comment('关键字');
            $table->timestamps();
            $table->foreign ('reply_id')->references ('id')->on ('wx_reply')->onDelete ('cascade');//外键
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wx_reply_keywords');
        Schema::dropIfExists('wx_reply');
    }
}
