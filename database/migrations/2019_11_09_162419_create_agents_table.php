<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateAgentsTable.
 */
class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('agents', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->char ('username', 20)->default ('')->comment ('用户名');
            $table->char ('agent_no', 20)->default ('')->comment ('代理商编号');
            $table->string ('agent_name', 50)->default ('')->comment ('代理商名称');
            $table->string ('password', 128)->default ('');
            $table->date ('birthday')->comment ('出生日期')->nullable ();
            $table->string ('wx_name', 100)->default ('')->comment ('绑定微信名称');
            $table->string ('company_name', 100)->default ('')->comment ('公司名称');
            $table->string ('contact_name', 20)->default ('')->comment ('联系人名称');
            $table->string ('contact_phone', 20)->default ('')->comment ('联系人号码');
            $table->unsignedBigInteger ('office_region_id')->default (0)->comment ('办公区域');
            $table->string ('office_address', 200)->default ('')->comment ('办公地址');
            $table->string ('authorize_no', 50)->default ('')->comment ('授权编号');
            $table->date ('authorize_date')->comment ('授权时长')->nullable ();
            $table->tinyInteger ('is_forever_authorize')->default (0)->comment ('是否永久授权');
            $table->date ('join_date')->comment ('加盟日期')->nullable ();
            $table->tinyInteger ('status')->default (0)->comment ('状态');
            $table->unsignedBigInteger ('admin_id')->default (0)->comment ('创建人');

            $table->timestamps ();
        });
        Schema::create ('agent_regions', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->unsignedBigInteger ('agent_id')->default (0);
            $table->unsignedBigInteger ('proxy_region_id')->default (0)->comment ('代理区域');
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
        Schema::drop ('agents');
        Schema::drop ('agent_regions');
    }
}
