<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlcCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plc_counters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('origin_group_code');
            $table->string('plc_counter');
            $table->integer('created_by');
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['origin_group_code', 'plc_counter'], 'plc_counter_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plc_counters');
    }
}
