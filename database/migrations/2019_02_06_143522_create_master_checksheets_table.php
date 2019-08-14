<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMasterChecksheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_checksheets', function (Blueprint $table) {
            $table->integer('id_input');
            $table->string('id_checkSheet');
            $table->increments('id');
            $table->string('countainer_number');
            $table->string('destination');
            $table->string('invoice');
            $table->string('seal_number');
            $table->string('no_pol');
            $table->string('etd_sub');
            $table->string('payment');
            $table->string('carier');
            $table->string('shipped_from');
            $table->string('shipped_to');
            $table->string('Stuffing_date');        
            $table->integer('status')->default('0');
            $table->string('reason');
            $table->integer('check_by')->default('0');
            $table->integer('created_by');
            $table->datetime('start_stuffing');
            $table->datetime('finish_stuffing');
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
        Schema::dropIfExists('master_checksheets');
    }
}
