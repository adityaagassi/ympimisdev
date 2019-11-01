<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQcCparItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_cpar_items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cpar_no');
            $table->string('part_item');
            $table->string('no_invoice');
            $table->integer('lot_qty');
            $table->integer('sample_qty');
            $table->string('detail_problem');
            $table->integer('defect_qty');
            $table->double('defect_presentase');
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
        Schema::dropIfExists('qc_cpar_items');
    }
}
