<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLabelingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('labelings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('section');
            $table->string('product');
            $table->date('date');
            $table->string('nama_mesin');
            $table->string('foto_arah_putaran');
            $table->string('foto_sisa_putaran');
            $table->string('keterangan');
            $table->string('send_status');
            $table->date('send_date');
            $table->string('approval_leader');
            $table->date('approved_date_leader');
            $table->string('approval');
            $table->date('approved_date');
            $table->string('created_by');
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
        Schema::dropIfExists('labelings');
    }
}
