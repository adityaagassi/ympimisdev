<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContainerSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('container_id')->unique();
            $table->string('container_code');
            $table->string('destination_code');
            $table->date('shipment_date');
            $table->string('container_number')->nullable();
            $table->string('att')->nullable();
            $table->integer('created_by');
            $table->softDeletes();
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
        Schema::dropIfExists('container_schedules');
    }
}
