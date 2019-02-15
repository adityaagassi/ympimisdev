<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetailChecksheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detail_checksheets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_checkSheet');
            $table->string('destination');
            $table->string('invoice');
            $table->string('gmc');
            $table->string('goods');
            $table->string('marking');
            $table->string('package_qty');
            $table->string('package_set');
            $table->string('qty_qty');
            $table->string('qty_set');
            $table->integer('confirm')->default('0');
            $table->integer('bara')->default('1');
            $table->string('markingcheck');
            $table->string('diff');
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
        Schema::dropIfExists('detail_checksheets');
    }
}
