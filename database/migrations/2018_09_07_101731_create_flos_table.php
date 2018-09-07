<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFlosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('flo_number')->unique();
            $table->string('invoice_number')->nullable();
            $table->string('container_number')->nullable();
            $table->date('bl_date')->nullable();
            $table->integer('shipment_schedule_id');
            $table->double('quantity');
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
        Schema::dropIfExists('flos');
    }
}
