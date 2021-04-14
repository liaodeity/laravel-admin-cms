<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {
        Schema::create ('config_groups', function (Blueprint $table) {
            $table->id ();
            $table->string ('name')->default ('')->comment ('组标识')->index ()->unique ();
            $table->string ('title')->default ('')->comment ('组名称');
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
        Schema::dropIfExists ('config_groups');
    }
}
