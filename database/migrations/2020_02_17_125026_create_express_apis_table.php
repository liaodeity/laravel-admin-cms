<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpressApisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_apis', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('delivery_no', 50)->default('')->comment('快递编号');
            $table->string('delivery_type', 50)->default('')->comment('快递类型');
            $table->longText('result_json')->comment('快递查询结果');
            $table->smallInteger('api_status')->default(0)->comment('状态');
            $table->tinyInteger('delivery_status')->default(0)->comment('快递状态');
            $table->tinyInteger('is_sign')->default(0)->comment('是否签收');
            $table->timestamp('last_express_at')->nullable()->comment('最后一次快递时间');
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
        Schema::dropIfExists('express_apis');
    }
}
