<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxAccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('wx_accounts', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->string ('openid', 128)->default ('')->comment ('微信用户的唯一标识');
            $table->string ('nickname', 30)->default ('')->comment ('用户昵称');
            $table->string ('sex', 3)->default ('')->comment ('用户的性别，值为1时是男性，值为2时是女性，值为0时是未知');
            $table->string ('province', 50)->default ('')->comment ('用户个人资料填写的省份');
            $table->string ('city', 50)->default ('')->comment ('普通用户个人资料填写的城市');
            $table->string ('country', 100)->default ('')->comment ('国家，如中国为CN');
            $table->string ('headimgurl')->default ('')->comment ('用户头像');
            $table->string ('subscribe',2)->default (0)->comment ('是否关注');
            $table->string ('unionid', 128)->default ('')->comment ('只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。');
            $table->unsignedBigInteger ('account_id')->default (0)->comment ('关联ID');
            $table->string ('account_type', 100)->default ('')->comment ('关联类型');
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
        Schema::dropIfExists ('wx_accounts');
    }
}
