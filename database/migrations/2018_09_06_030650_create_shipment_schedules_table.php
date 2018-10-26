<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShipmentSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipment_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->date('st_month');
            $table->string('sales_order');
            $table->string('shipment_condition_code');
            $table->string('destination_code');
            $table->string('material_number');
            $table->string('hpl');
            $table->date('st_date');
            $table->date('bl_date');
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
        Schema::dropIfExists('shipment_schedules');
    }
}
