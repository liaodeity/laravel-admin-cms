<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSerialNumberTable extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up ()
    {
        Schema::create ('serial_numbers', function (Blueprint $table) {
            $table->bigIncrements ('id');
            $table->unsignedBigInteger ('serial_id')->default (0)->comment ('关联ID');
            $table->string ('serial_type', 100)->default ('')->comment ('关联类型');
            $table->timestamps ();
        });
        DB::statement ("ALTER TABLE " . DB::getConfig ('prefix') . "serial_numbers AUTO_INCREMENT = 10000001;");
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down ()
    {
        Schema::dropIfExists ('serial_numbers');
    }
}
