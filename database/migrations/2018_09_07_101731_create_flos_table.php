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
            $table->string('container_id')->nullable();
            $table->date('bl_date')->nullable();
            $table->integer('shipment_schedule_id');
            $table->string('material_number');
            $table->double('quantity')->default('0');
            $table->double('actual')->default('0');
            $table->string('status')->default('0');
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
