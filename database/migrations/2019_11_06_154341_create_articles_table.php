<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateArticlesTable.
 */
class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('articles', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->string ('title', 100)->default ('')->comment ('标题');
            $table->text ('content')->comment ('内容');
            $table->string ('push_source', 50)->default ('')->comment ('发布来源');
            $table->unsignedBigInteger ('view_number')->default (0)->comment ('浏览次数');
            $table->tinyInteger ('status')->default (0);
            $table->timestamps ();
        });
        Schema::create ('article_reads', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->unsignedBigInteger ('article_id')->default (0);
            $table->unsignedBigInteger ('agent_id')->default (0);
            $table->tinyInteger ('is_read')->default (0)->comment ('是否已读');
            $table->timestamps ();
            $table->foreign ('article_id')->references ('id')->on ('articles')->onDelete ('cascade');//外键
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::drop ('article_reads');
        Schema::drop ('articles');
    }
}
