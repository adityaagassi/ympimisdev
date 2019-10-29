<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSamplingChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sampling_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_list_id');
            $table->string('department');
            $table->string('section');
            $table->string('subsection');
            $table->string('month');
            $table->date('date');
            $table->string('product');
            $table->string('no_seri_part');
            $table->string('jumlah_cek');
            $table->string('leader');
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
        Schema::dropIfExists('sampling_checks');
    }
}
