<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateArticleCatesTable.
 */
class CreateArticleCatesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_cates', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string ('cate_name',50)->default ('')->comment ('分类名称');
            $table->string ('cate_desc',200)->default ('')->comment ('分类说明');
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('article_cates');
	}
}
