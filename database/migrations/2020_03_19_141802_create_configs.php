<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('configs', function (Blueprint $table) {
            $table->id ();
            $table->foreignId ('group_id')->constrained ('config_groups')->cascadeOnDelete ();
            $table->string ('name')->default ('')->comment ('配置名称')->index ()->unique ();
            $table->string ('title')->default ('')->comment ('配置标题');
            $table->tinyInteger ('type')->default (0)->comment ('类型');
            $table->text ('content')->nullable ()->comment ('内容');
            $table->text ('param_json')->nullable ()->comment ('配置选项');
            $table->string ('description', 1000)->default ('')->comment ('描述');
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
        Schema::dropIfExists ('configs');
    }
}
