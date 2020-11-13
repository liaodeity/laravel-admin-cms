<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMembersTable.
 */
class CreateMembersTable extends Migration
{
	/**
	 * Run the migrations.
	 * @return void
	 */
	public function up()
	{
		Schema::create('members', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->char('member_no', 20)->default('')->comment('会员编号');
			$table->string('wx_account', 50)->default('')->comment('微信号');
			$table->string('real_name', 20)->default('')->comment('真实姓名');
			$table->unsignedBigInteger('wx_qr_code')->default(0)->comment('微信个人二维码');
			$table->string('wx_name', 100)->default('')->comment('绑定微信名称');
			$table->char('mobile', 20)->default('')->comment('会员手机号');
			$table->date('birthday')->nullable ()->comment('出生日期');
			$table->string('gender', 4)->default('')->comment('性别');
			$table->string('work_type', 100)->default('')->comment('工种');
			$table->string('working_year', 100)->default('')->comment('从业年限');
			$table->unsignedBigInteger('native_region_id')->default(0)->comment('籍贯区域');
			$table->unsignedBigInteger('resident_region_id')->default(0)->comment('常驻区域');
			$table->string('resident_address', 100)->default('')->comment('常驻地址');
			$table->text('business_channel')->comment('业务渠道');
			$table->date('reg_date')->nullable ()->comment('注册时间');
			$table->timestamp('last_login_at')->nullable ()->comment('最后登录时间');
			$table->tinyInteger('status')->default (0)->comment('会员状态');
			$table->timestamps();
		});
		Schema::create('member_agents', function (Blueprint $table) {
			$table->bigIncrements('id');
			$table->unsignedBigInteger('member_id')->default (0)->comment('会员ID');
			$table->unsignedBigInteger('agent_id')->default (0)->comment('代理商名称');
			$table->unsignedBigInteger('referrer_member_id')->default(0)->comment('推荐人');
			$table->char('lat', 20)->default('')->comment('纬度');
			$table->char('lng', 20)->default('')->comment('经度');
			$table->string('loc_address', 50)->default('')->comment('坐标地址');
			$table->tinyInteger('is_allow_subordinate')->default (1)->comment('是否允许下级');
			$table->decimal('bill_rate')->default(0)->comment('佣金比例');
            $table->tinyInteger('sp_status')->default(0)->comment('审核状态');
			$table->timestamps();
			$table->foreign ('member_id')->references ('id')->on ('members')->onDelete ('cascade');//外键
		});
		//Schema::create('member_pics', function (Blueprint $table) {
		//	$table->bigIncrements('id');
		//	$table->unsignedBigInteger('member_agent_id')->comment('会员代理商ID');
		//	$table->unsignedBigInteger('pic_id')->comment('图片ID');
		//
		//	$table->timestamps();
		//});
	}

	/**
	 * Reverse the migrations.
	 * @return void
	 */
	public function down()
	{
        Schema::drop('member_agents');
        //Schema::drop('member_pics');
		Schema::drop('members');

	}
}
